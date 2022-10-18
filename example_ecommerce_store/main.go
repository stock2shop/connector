package main

import (
	"bytes"
	"encoding/json"
	"errors"
	"fmt"
	"github.com/gorilla/mux"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"sort"
	"strconv"
	"strings"
	"time"
)

func main() {
	router := mux.NewRouter()
	router.HandleFunc("/products", PutProducts).Methods("POST")
	router.HandleFunc("/products", GetProducts).Methods("GET")
	router.HandleFunc("/products/page", GetProductsPage).Methods("GET")

	// os.Args[0] is the program
	port := os.Args[1]
	if port == "" {
		log.Fatal("server port must be specified as the first argument")
	}

	path := os.Args[2]
	if path == "" {
		log.Fatal("data storage path must be specified as the second argument")
	}

	log.Printf("server stating on port %s", port)
	log.Fatal(http.ListenAndServe(fmt.Sprintf(":%s", port), router))
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
	dataPath := fmt.Sprintf("%s", os.Args[2])
	products := Products{}

	// populate products with data from request
	err := json.NewDecoder(r.Body).Decode(&products)
	if err != nil {
		Response(w, http.StatusBadRequest, err.Error())
		return
	}

	// validate products
	err = products.Validate()
	if err != nil {
		Response(w, http.StatusBadRequest, err.Error())
		return
	}

	// write products to file, generate id if it is not set
	for i := 0; i < len(products); i++ {
		if products[i].ID == "" {
			products[i].ID = strconv.Itoa(int(time.Now().UnixNano()))
		}

		data, err := json.MarshalIndent(products[i], "", "    ")
		if err != nil {
			Response(w, http.StatusBadRequest, err.Error())
			return
		}

		err = os.WriteFile(fmt.Sprintf("%s/%s.json", dataPath, products[i].ID), data, 0644)
		if err != nil {
			Response(w, http.StatusBadRequest, err.Error())
			return
		}
	}

	Response(w, http.StatusAccepted, products)
}

func GetProducts(w http.ResponseWriter, r *http.Request) {
	dataPath := fmt.Sprintf("%s", os.Args[2])
	getProducts := GetProduct{}

	// populate products with data from request
	err := json.NewDecoder(r.Body).Decode(&getProducts)
	if err != nil {
		Response(w, http.StatusBadRequest, err.Error())
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
		filePath := fmt.Sprintf("%s/%s.json", dataPath, id)
		b, err := os.ReadFile(filePath)
		if err != nil {
			Response(w, http.StatusBadRequest, fmt.Sprintf("unable to read file: %v.json", id))
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
		Response(w, http.StatusAccepted, err.Error())
		return
	}

	Response(w, http.StatusAccepted, products)

}

func GetProductsPage(w http.ResponseWriter, r *http.Request) {
	dataPath := fmt.Sprintf("%s", os.Args[2])

	// get offset, default to 0 if not included in url params
	o := r.URL.Query().Get("offset")
	if o == "" {
		o = "0"
	}
	offset, err := strconv.Atoi(o)
	if err != nil {
		Response(w, http.StatusBadRequest, "invalid offset")
		return
	}

	// get offset, default to 10 if not included in url params
	l := r.URL.Query().Get("limit")
	if l == "" {
		o = "10"
	}
	limit, err := strconv.Atoi(l)
	if err != nil {
		Response(w, http.StatusBadRequest, "invalid limit")
		return
	}

	var files []string
	err = filepath.Walk(dataPath, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			fmt.Println(err)
			return err
		}

		if !info.IsDir() && strings.HasSuffix(path, ".json") {
			files = append(files, path)
		}
		return nil
	})
	if err != nil {
		Response(w, http.StatusBadRequest, err.Error())
		return
	}

	sort.Strings(files)

	if limit == 0 {
		Response(w, http.StatusAccepted, Products{})
		return
	}
	if offset >= len(files) {
		Response(w, http.StatusAccepted, Products{})
		return
	}

	// read file data and append to buffer
	buf := new(bytes.Buffer)
	buf.Write([]byte("["))

	for i := 0; i < limit; i++ {
		if offset + i >= len(files) {
			break
		}

		b, err := os.ReadFile(files[offset + i])
		if err != nil {
			Response(w, http.StatusBadRequest, fmt.Sprintf("unable to read file: %v", files[offset + i]))
			return
		}
		buf.Write(b)

		if (i + 1 < limit) && (offset + i + 1 < len(files)) {
			buf.Write([]byte(","))
		}
	}
	buf.Write([]byte("]"))

	// unmarshal into response type
	products := Products{}
	err = json.Unmarshal(buf.Bytes(), &products)
	if err != nil {
		Response(w, http.StatusAccepted, err.Error())
		return
	}

	Response(w, http.StatusAccepted, products)
}