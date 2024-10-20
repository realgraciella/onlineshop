function toggleDropdown() {
    var dropdown = document.getElementById("categoryDropdown");
    dropdown.style.display = (dropdown.style.display === "none") ? "block" : "none";
}


const button = document.querySelector('.button');
button.addEventListener('click', () => {
  alert('You clicked the Add Service button!');
});