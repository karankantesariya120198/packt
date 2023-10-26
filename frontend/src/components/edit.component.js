import React, { useEffect, useState } from "react";
import * as Yup from "yup";
import { useFormik } from "formik";
import { Form, Button, Row, Col, Card } from "react-bootstrap";
import { useNavigate, useParams, Link } from 'react-router-dom'
import axios from 'axios';
import Swal from 'sweetalert2';

export default function EditUser() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({});
  const [image, setImage] = useState();
  const { id } = useParams();
  const [validationError,setValidationError] = useState({});

  
  useEffect(()=>{
    fetchProduct();
  },[])

  
  const fetchProduct = async () => {
    await axios.get(`http://localhost:8000/api/books/${id}`).then(({data})=>{
      var initialValues = {
        title: data.data.title ? data.data.title : "",
        author: data.data.author ? data.data.author : "",
        genre: data.data.genre ? data.data.genre : "",
        description: data.data.genre ? data.data.genre : "",
        // image: data.data.image ? data.data.image : "",
        isbn: data.data.isbn ? data.data.isbn : "",
        published: data.data.published ? data.data.published : "",
        publisher: data.data.publisher ? data.data.publisher : "",
      };
      setFormData(initialValues);
      setImage(data.data.image);
    }).catch(({response:{data}})=>{
      Swal.fire({
        text:data.message,
        icon:"error"
      })
    })
  }
  
  const MAX_FILE_SIZE = 2097152; //2MB

    const validFileExtensions = { image: ['jpg', 'png', 'jpeg'] };

    function isValidFileType(fileName, fileType) {
      return fileName && validFileExtensions[fileType].indexOf(fileName.split('.').pop()) > -1;
    }

    const bookSchema = Yup.object({
      title: Yup.string()
                .max(100, "Title must be at least 100 characters")
                .required("Please enter your book title")
                .matches(
                  /^[A-Za-z0-9 ]+$/, 
                  "Only Alphabets, Number and Space are allowed for this field"
                ),

      author: Yup.string()
                  .max(100, "Author must be at least 100 characters")
                  .required("Please enter your book author")
                  .matches(
                    /^[A-Za-z0-9 ]+$/, 
                    "Only Alphabets, Number and Space are allowed for this field"
                  ),

      genre: Yup.string()
                .max(100, "Genre must be at least 100 characters")
                .required("Please enter your book genre")
                .matches(
                  /^[A-Za-z0-9 ]+$/, 
                  "Only Alphabets, Number and Space are allowed for this field"
                ),

      description: Yup.string()
                      .max(500,"Description must be at least 500 characters")
                      .required("Please enter your book description")
                      .matches(/^[A-Za-z0-9 ]+$/, "Only Alphabets, Number and Space are allowed for this field"),

      isbn: Yup.string()
                .max(40,"Isbn must be at least 40 characters")
                .required("Please enter your book isbn")
                .matches(/^[A-Za-z0-9 ]+$/, "Only Alphabets, Number and Space are allowed for this field"),

      publisher: Yup.string()
                    .max(40,"Publisher must be at least 40 characters")
                    .required("Please enter your book publisher")
                    .matches(
                      /^[A-Za-z0-9 ]+$/, 
                      "Only Alphabets, Number and Space are allowed for this field"
                    ),
      
      image: Yup.mixed()
                  .test("is-valid-type", "Please upload Image file",
                      value => isValidFileType(value && value.name.toLowerCase(), "image"))
                  .test("is-valid-size", "Book image size must be less than 2 MB",
                      value => value && value.size <= MAX_FILE_SIZE),

      published: Yup.date()
                    .required("Please select your book publish date"),

    });
      
    const { values, errors, touched, setFieldValue , handleChange, handleSubmit } =
    useFormik({
        initialValues: formData,
        validationSchema: bookSchema,
        enableReinitialize: true,
        onSubmit: async (values) => {
            const formData = new FormData()
            formData.append('_method', 'PUT')
            formData.append('title', values.title)
            formData.append('author', values.author)
            formData.append('genre', values.genre)
            formData.append('description', values.description)
            formData.append('isbn', values.isbn)
            if(values.image!==null){
              formData.append('image', values.image)
            }
            formData.append('published', values.published)
            formData.append('publisher', values.publisher)

            await axios.post(`http://localhost:8000/api/books/${id}`, formData)
                  .then((res)=>{
                    if(res.data.result === 1) {
                      Swal.fire({
                        toast: true,
                        position: 'top-end',
                        title: res.data.message,
                        type: "success",
                        icon: "success",
                        showConfirmButton: false,
                        showCloseButton: true,
                        timerProgressBar: true,
                        timer: 3000
                      });
                      navigate("/");
                    } else if(res.data.statusCode === 400) {
                      setValidationError(res.data.data);  
                    }
                  }).catch((err)=>{
                    var error = err.response.data.message;
                    setValidationError(error);
                  })
        },
    });

  return (
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-12 col-sm-12">
          <div className="card">
            <div className="card-body">
              <div className="row">
                <div className="col-3">
                  <h4 className="card-title">Edit Book</h4>
                </div>
                <div className="col-9">
                  <Link to="/">
                    <button className="card-title float-end btn btn-sm btn-outline-primary">Back</button>
                  </Link>
                </div>
              </div>
              <hr />
              <div className="form-wrapper">
                {
                  Object.keys(validationError).length > 0 && (
                    <div className="row">
                      <div className="col-12">
                        <div className="alert alert-danger">
                          <ul className="mb-0">
                            {
                              Object.entries(validationError).map(([key, value])=>(
                                <li key={key}>{value}</li>   
                              ))
                            }
                          </ul>
                        </div>
                      </div>
                    </div>
                  )
                }
                  <Form
                    action="#" 
                    name="bookEditForm" 
                    id="bookEditForm" 
                    method="POST" 
                    encType="multipart/form-data"
                  >
                    <Row> 
                      <Col>
                        <Form.Group controlId="title">
                            <Form.Label>
                              Title
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              placeholder="Enter your book title"
                              name="title"
                              maxLength={100}
                              value={values.title} 
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.title && touched.title && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.title}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                      <Col>
                        <Form.Group controlId="author">
                            <Form.Label>
                              Author
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              placeholder="Enter your book author"
                              name="author"
                              maxLength={100}
                              value={values.author} 
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.author && touched.author && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.author}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                    </Row>
                    <Row>
                      <Col>
                        <Form.Group controlId="genre" className="mt-2">
                            <Form.Label>
                              Genre
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              placeholder="Enter your book genre"
                              name="genre"
                              maxLength={100}
                              value={values.genre} 
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.genre && touched.genre && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.genre}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                      <Col>
                        <Form.Group controlId="description" className="mt-2">
                            <Form.Label>
                              Description
                            </Form.Label>
                            <Form.Control
                              type="textarea"
                              as="textarea"
                              placeholder="Enter your book description"
                              name="description"
                              maxLength={500}
                              value={values.description}
                              onChange={handleChange}
                              autoComplete="off"
                              required
                              style={{ height: '100px' }}
                            />
                            {errors.description && touched.description && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.description}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                    </Row>
                    <Row>
                      <Col>
                        <Form.Group controlId="isbn" className="mt-2">
                            <Form.Label>
                              Isbn
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              placeholder="Enter your book isbn"
                              name="isbn"
                              maxLength={40}
                              value={values.isbn} 
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.isbn && touched.isbn && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.isbn}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                      <Col>
                        <Form.Group controlId="image" className="mt-2">
                            <Form.Label>
                              Image
                            </Form.Label>
                            <Form.Control 
                              type="file"
                              name="image"
                              accept="image/jpg,image/jpeg,image/png"
                              onChange={(e)=>setFieldValue("image",e.target.files[0])} 
                                
                            />
                            {errors.image && touched.image && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.image}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                    </Row>
                    <Row>
                      <Col className="col-2">
                        <Form.Group controlId="imagePreview" className="mt-2">
                          <Form.Label>Old Image Preview</Form.Label>      
                          <Card.Img src={image} height={100} width={100} />
                        </Form.Group>
                      </Col>
                    </Row>
                    <Row>
                      <Col>
                        <Form.Group controlId="published" className="mt-2">
                            <Form.Label>
                              Published
                            </Form.Label>
                            <Form.Control 
                              type="date"
                              name="published"
                              value={values.published}
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.published && touched.published && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.published}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>
                      <Col>
                        <Form.Group controlId="publisher" className="mt-2">
                            <Form.Label>
                              Publisher
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              name="publisher"
                              maxLength={40}
                              value={values.publisher}
                              onChange={handleChange}
                              autoComplete="off"
                              required
                            />
                            {errors.publisher && touched.publisher && (
                              <Form.Text size="sm" className="text-danger">
                                {errors.publisher}
                              </Form.Text>   
                            )}
                        </Form.Group>
                      </Col>  
                    </Row>
                    <Row>
                      <Col>
                        <Button 
                          variant="primary" 
                          className="mt-2 justify-content-end" 
                          size="sm" 
                          block="block" 
                          type="submit"
                          onClick={handleSubmit}
                        >
                          Save
                        </Button>
                      </Col>
                    </Row>
                  </Form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}