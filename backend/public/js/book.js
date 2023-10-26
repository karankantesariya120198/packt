$(document).ready(function(){
    // DataTable
    $('#bookTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: BookListURL,
        columns: [
                { 
                    data: 'id',
                    orderable: false,     
                },
                { 
                    data: 'image',
                    orderable: false,
                },
                { 
                    data: 'title',
                    orderable: false,
                },
                { 
                    data: 'author',
                    orderable: false,
                },
                { 
                    data: 'genre',
                    orderable: false,
                },
                { 
                    data: 'description',
                    orderable: false,
                },
                { 
                    data: 'isbn',
                    orderable: false,
                },
                { 
                    data: 'published',
                    orderable: false,
                },
                { 
                    data: 'publisher',
                    orderable: false,
                },
                { 
                    data: 'action',
                    orderable: false,
                }
        ]
    });

    var someTableDT = $("#bookTable").on("draw.dt", function () {
        $(this).find(".dataTables_empty").parents('tbody').empty();
    });

    var editBookForm = $("form[name='editBookForm']");
    
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= 2097152)
    }, 'Image size must be less than 2 MB.');

    editBookForm.validate({
        ignore: [],
        errorClass: 'text-danger small',
        rules: {
            title: {
                required: true,
            },
            author: {
                required: true,
            },
            genre: {
                required: true,
            },
            description: {
                required: true,
            },
            isbn: {
                required: true,
            },
            image: {
                required: true,
                filesize: 2,
                extension: "jpg|jpeg|png"
            },
            published: {
                required: true,
            },
            publisher: {
                required: true,
            }
        },
        messages: {
            title: {
                required: "Please enter book title.",
            },
            author: {
                required: "Please enter book author.",
            },
            genre: {
                required: "Please enter book genre.",
            },
            description: {
                required: "Please enter book description.",
            },
            isbn: {
                required: "Please enter book isbn.",
            },
            image: {
                required: "Please select book image.",
                extension: "Please upload provided extenstion image."
            },
            published: {
                required: "Please select book published date."
            },
            publisher: {
                required: "Please enter book publisher."
            }
        },
        highlight: function(element, errorClass) {
            $(element).parents("div.control-group").addClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).parents(".error").removeClass(errorClass);
        }
    });
});

$("body").on("click", "#btnBookEdit", function (e) {
    e.preventDefault();
    if ($(editBookForm).valid()) {
        $(editBookForm).submit();
    }
});

$("body").on("click", "#deleteBook", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to delete!") == true) {
        var id = $(this).data("id");
        var token = $("meta[name='csrf-token']").attr("content");
    
        $.ajax({
            url: BASEURL+'/admin/books/delete/'+id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
            success: function (){
                console.log("Check data");
                $('#bookTable').DataTable().ajax.reload();
            }
        });
    }
});