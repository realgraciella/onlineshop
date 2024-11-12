const form = document.getElementById('category-form');
const tableBody = document.querySelector('#category-table tbody');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const categoryName = document.getElementById('category-name').value;

    if (categoryName !== '') {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `<td>${categoryName}</td>`;
        tableBody.appendChild(newRow);

        document.getElementById('category-name').value = '';
    }
});


$(document).ready(function() {
$('#category-form').submit(function(e) {
    e.preventDefault();

    $.ajax({
        type: 'POST',
        url: 'add_category.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            alert(response.message);
            if (response.status === 'success') {
                updateCategoryTable();
                $('#category-name').val('');
            }
        },
        error: function() {
            alert('Error submitting the form.');
        }
    });
});

document.getElementById("productForm").addEventListener("submit", function(e) {
  e.preventDefault();
  
  // get form data
  var productName = document.getElementById("productName").value;
  var brandName = document.getElementById("brandName").value;
  var productCategory = document.getElementById("productCategory").value;
  var price = document.getElementById("price").value;
  
  // perform validation or other operations on the form data
  
  // show success message
  document.getElementById("productForm").reset();
  alert("Product added successfully!");
});

document.getElementById("brandForm").addEventListener("submit", function(e) {
  e.preventDefault();
  
  // get form data
  var brandName = document.getElementById("brandName").value;
  
  // create a new table row
  var newRow = document.createElement("tr");
  newRow.innerHTML = `
    <td>${new Date().getTime()}</td>
    <td>${brandName}</td>
  `;
  
  // append new row to the table
  document.getElementById("brandTable").getElementsByTagName("tbody")[0].appendChild(newRow);

  // reset form field
  document.getElementById("brandForm").reset();
});