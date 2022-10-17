package main

import (
	"bytes"
	"encoding/json"
	"errors"
	"fmt"
	"github.com/google/uuid"
	"github.com/gorilla/mux"
	"log"
	"net/http"
	"os"
)

func main() {
	router := mux.NewRouter()
	router.HandleFunc("/products", PutProducts).Methods("POST")
	router.HandleFunc("/products", GetProducts).Methods("GET")
	log.Printf("server stating on port 8080")
	log.Fatal(http.ListenAndServe(":8080", router))
}

func Response(w http.ResponseWriter, statusCode int, data interface{}) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(statusCode)

	err := json.NewEncoder(w).Encode(data)
	if err != nil {
		log.Printf("%v", err)
	}
}

type Product struct {
	Title string   `json:"title"`
	ID    string   `json:"id"`
	Skus  []string `json:"skus"`
}

type Products []Product

type GetProduct []string

func (p *Product) Validate() error {
	if p.Title == "" {
		err := errors.New("product Title is required")
		return err
	}
	if len(p.Skus) == 0 {
		err := errors.New("product Skus are required")
		return err
	}
	return nil
}

func (p *Products) Validate() error {
	for _, product := range *p {
		err := product.Validate()
		if err != nil {
			return err
		}
	}
	return nil
}

func PutProducts(w http.ResponseWriter, r *http.Request) {
	products := Products{}

	// populate products with data from request
	err := json.NewDecoder(r.Body).Decode(&products)
	if err != nil {
		Response(w, http.StatusBadRequest, err)
		return
	}

	// validate products
	err = products.Validate()
	if err != nil {
		Response(w, http.StatusBadRequest, err)
		return
	}

	// write products to file, generate id if it is not set
	for i := 0; i < len(products); i++ {
		if products[i].ID == "" {
			id := uuid.New()
			products[i].ID = id.String()
		}

		data, err := json.MarshalIndent(products[i], "", "")
		if err != nil {
			Response(w, http.StatusBadRequest, err)
			return
		}

		err = os.WriteFile(fmt.Sprintf("./%s.json", products[i].ID), data, 0644)
		if err != nil {
			Response(w, http.StatusBadRequest, err)
			return
		}
	}

	Response(w, http.StatusAccepted, products)
}

func GetProducts(w http.ResponseWriter, r *http.Request) {
	getProducts := GetProduct{}

	// populate products with data from request
	err := json.NewDecoder(r.Body).Decode(&getProducts)
	if err != nil {
		Response(w, http.StatusBadRequest, err)
		return
	}

	// check that there are id's to find
	if len(getProducts) < 1 {
		Response(w, http.StatusAccepted, getProducts)
		return
	}

	// read file data and append to buffer
	buf := new(bytes.Buffer)
	buf.Write([]byte("["))
	for i, id := range getProducts {
		b, err := os.ReadFile(fmt.Sprintf("%s.json", id))
		if err != nil {
			Response(w, http.StatusBadRequest, err)
			return
		}
		buf.Write(b)

		if i < len(getProducts) - 1 {
			buf.Write([]byte(","))
		}
	}
	buf.Write([]byte("]"))

	// unmarshal into response type
	products := Products{}
	err = json.Unmarshal(buf.Bytes(), &products)
	if err != nil {
		Response(w, http.StatusAccepted, err)
		return
	}

	Response(w, http.StatusAccepted, products)

}