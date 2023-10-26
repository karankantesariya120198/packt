import * as React from "react";
import Navbar from "react-bootstrap/Navbar";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import "bootstrap/dist/css/bootstrap.css";

import { BrowserRouter as Router , Routes, Route, Link } from "react-router-dom";

import EditBook from "../src/components/edit.component";
import BookList from "../src/components/list.component";
import CreateBook from "../src/components/create.component";
import ViewBook from "../src/components/view.component";

function App() {
    return (
      <Router>
        <Navbar className="bg-body-tertiary">
          <Container>
            <Navbar.Brand className="navbar-tag">
              <img
                src={window.location.origin + '/logo.png'}
                height="50"
                className="d-inline-block"
                alt="Book Store logo"
              />
              <Link to={"/"} className="navbar-brand">
                <b>BOOKSTORE</b>
              </Link>
            </Navbar.Brand>
          </Container>
        </Navbar>

        <Container className="mt-4">
          <Row>
            <Col md={12}>
              <Routes>
                <Route 
                  path="/books/create" 
                  element={<CreateBook />} 
                />
                <Route 
                  path="/books/edit/:id" 
                  element={<EditBook />} 
                />
                <Route 
                  path="/books/show/:id" 
                  element={<ViewBook />} 
                />
                <Route 
                  exact path='/' 
                  element={<BookList />} 
                />
              </Routes>
            </Col>
          </Row>
        </Container>
      </Router>
    );
}

export default App;