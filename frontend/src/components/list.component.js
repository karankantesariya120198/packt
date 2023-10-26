import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Button from 'react-bootstrap/Button'
import axios from 'axios';
import Swal from 'sweetalert2'
import Pagination from "react-js-pagination";

export default function List() {

    const [books, setBooks] = useState([])
    const [data, setData] = useState()
    const [loader, setLoader] = useState(true);
    
    useEffect((pageNumber = 1, search = '')=>{
        fetchBooks(pageNumber, search) 
    },[])

    const fetchBooks = async (pageNumber = 1, search = '') => {
        await axios.get(`http://localhost:8000/api/books?page=${pageNumber}&search=${search}`).then(({data})=>{
            setBooks(data.data.data)
            setData(data.data)
            setLoader(false)
        })
    }

    const deleteBooks = async (id) => {
        const isConfirm = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            return result.isConfirmed
        });

        if(!isConfirm){
            return;
        }

        await axios.delete(`http://localhost:8000/api/books/${id}`).then(({data})=>{
            Swal.fire({
                toast: true,
                position: 'top-end',
                title: data.message,
                type: "success",
                icon: "success",
                showConfirmButton: false,
                showCloseButton: true,
                timerProgressBar: true,
                timer: 3000
            });
            fetchBooks()
        }).catch((err)=>{
            var error = err.response.data.message;
            Swal.fire({
                toast: true,
                position: 'top-end',
                title: error,
                type: "error",
                icon: "error",
                showConfirmButton: false,
                showCloseButton: true,
                timerProgressBar: true,
                timer: 3000
            });
        })
    }
    
    return (
        <div className="container">
            <div className="row">
                <div className='col-12'>
                    <Link 
                        className='btn btn-sm btn-primary mb-2 float-end' 
                        to={"/books/create"}
                    >
                        Create Book
                    </Link>
                </div>
                <div className='card'>
                    <div className='card-header'>
                        <div className='row'>
                            <div className='col-2'>
                                <b>Book List</b>
                            </div>
                            <div className='col-10 text-end'>
                                <b>Search: </b>
                                <input 
                                    type='text'
                                    name="search"
                                    onChange={(e) => {
                                        fetchBooks(1,e.target.value)
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                    <div className='card-body'>
                        <div className="col-12">
                            <div className="table-responsive">
                                <table className="table table-bordered mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Genre</th>
                                            <th>Description</th>
                                            <th>Isbn</th>
                                            <th>Image</th>
                                            <th>Published</th>
                                            <th>Publisher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        { 
                                            loader && (            
                                                <div class="spinner-grow d-flex justify-content-center align-items-center" role="status">
                                                    <span class="sr-only"></span>
                                                </div>
                                            )
                                        }
                                        {   
                                            books.length > 0 && (
                                                books.map((row, key)=>(
                                                    <tr key={key}>
                                                        <td>
                                                            <Link to={`/books/show/${row.id}`}>
                                                                {row.title}
                                                            </Link>
                                                        </td>
                                                        <td>{row.author}</td>
                                                        <td>{row.genre}</td>
                                                        <td>{row.description}</td>
                                                        <td>{row.isbn}</td>
                                                        <td>
                                                            <img alt={row.image} width="70px" src={row.image} />
                                                        </td>
                                                        <td>{row.published}</td>
                                                        <td>{row.publisher}</td>
                                                        <td>
                                                            <Link to={`/books/edit/${row.id}`} className='btn btn-success me-2'>
                                                                Edit
                                                            </Link>
                                                            <Button variant="danger" onClick={()=>deleteBooks(row.id)}>
                                                                Delete
                                                            </Button>
                                                        </td>
                                                    </tr>
                                                ))
                                            )
                                        }
                                    </tbody>
                                </table>
                                <div className='float-end mt-2'>
                                    <Pagination
                                        activePage={data?.current_page ? data?.current_page : 0}
                                        itemsCountPerPage={data?.per_page ? data?.per_page : 0 }
                                        totalItemsCount={data?.total ? data?.total : 0}
                                        onChange={(pageNumber) => {
                                            fetchBooks(pageNumber)
                                        }}
                                        pageRangeDisplayed={5}
                                        itemClass="page-item"
                                        linkClass="page-link"
                                        firstPageText="First Page"
                                        lastPageText="Last Lage"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}