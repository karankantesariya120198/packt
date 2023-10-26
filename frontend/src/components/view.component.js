import React, { useEffect, useState } from "react";
import { Form, Row, Col, Card } from "react-bootstrap";
import { useParams, Link } from 'react-router-dom'
import axios from 'axios';
import Swal from 'sweetalert2';

export default function EditUser() {
  const [formData, setFormData] = useState({});
  const { id } = useParams();

  useEffect(()=>{
    fetchProduct();
  },[])
  
  const fetchProduct = async () => {
    await axios.get(`http://localhost:8000/api/books/${id}`).then(({data})=>{
      setFormData(data.data);
    }).catch(({response:{data}})=>{
      Swal.fire({
        text:data.message,
        icon:"error"
      })
    })
  }
  
  return (
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-12 col-sm-12">
          <div className="card">
            <div className="card-body">
              <div className="row">
                <div className="col-3">
                  <h4 className="card-title">Book Details</h4>
                </div>
                <div className="col-9">
                  <Link to="/">
                    <button className="card-title float-end btn btn-sm btn-outline-primary">Back</button>
                  </Link>
                </div>
              </div>
              <hr />
              <div className="form-wrapper">
                  <Form>
                    <Row> 
                      <Col>
                        <Form.Group controlId="title">
                            <Form.Label>
                              Title
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              name="title"
                              value={formData.title}
                              readOnly
                            />
                        </Form.Group>
                      </Col>
                      <Col>
                        <Form.Group controlId="author">
                            <Form.Label>
                              Author
                            </Form.Label>
                            <Form.Control 
                              type="text"
                              name="author"
                              value={formData.author}
                              readOnly
                            />
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
                              name="genre"
                              value={formData.genre}
                              readOnly
                            />
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
                              name="description"
                              value={formData.description}
                              readOnly
                              style={{ height: '100px' }}
                            />
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
                              name="isbn"
                              value={formData.isbn}
                              readOnly
                            />
                        </Form.Group>
                      </Col>
                      <Col className="col-2">
                        <Form.Group controlId="imagePreview" className="mt-2">
                          <Form.Label>Image</Form.Label>      
                          <Card.Img src={formData.image} height={100} width={100} />
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
                              type="text"
                              name="published"
                              value={formData.published}
                              readOnly
                            />
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
                              value={formData.publisher}
                              readOnly
                            />
                        </Form.Group>
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