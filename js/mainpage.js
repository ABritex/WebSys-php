window.addEventListener("scroll", function () {
    var header = document.querySelector("header");
    header.classList.toggle("Sticky", window.scrollY > 0);
});

const carouselItems = document.querySelectorAll(".head_image");
let i = 1;

setInterval(() => {
    Array.from(carouselItems).forEach((item, index) => {

        if (i < carouselItems.length) {
            item.style.transform = `translateX(-${i * 100}%)`
        }
    })


    if (i < carouselItems.length) {
        i++;
    }
    else {
        i = 0;
    }
}, 2000)

let popularBooks = null;
let mostReadBooks = null;

fetch('json/booklibrary.json')
    .then(response => response.json())
    .then(data => {
        popularBooks = data.popular_books;
        mostReadBooks = data.most_read_books;
        isekaiBooks = data.isekai_books;
        romanceBooks = data.romance_books;

        addDataToHTML();
    })

function addDataToHTML() {
    renderBooks(popularBooks, '.popular_list');
    renderBooks(mostReadBooks, '.most_read_books');
    renderBooks(isekaiBooks, '.isekai_list');
    renderBooks(romanceBooks, '.romance_books');


}

function renderBooks(booksData, containerSelector) {
    let booksHTML = document.querySelector(containerSelector);
    booksHTML.innerHTML = '';
    if (booksData != null) {
        booksData.forEach(book => {
            let newBook = document.createElement('div');
            newBook.classList.add('item');
            newBook.innerHTML =
                `<a href="novel.php?id=${book.id}&name=${encodeURIComponent(book.name)}">
                <img src="${book.image}" alt="">
                <h1>+ Add to list</h1>
                ${book.name}
                </a>
                 <button onclick="addToList('${book.id}')">+ Add to list</button> 
                    <div class="sub_info">
                        <h2>${book.name}</h2>
                        <div class="h3-container"> 
                            <h3>${book.volume}</h3>
                            <hr class='vertical-line'>
                            <h3>${book.publisher}</h3>
                            <hr class='vertical-line'>
                            <h3>${book.year}</h3>
                            <hr class='vertical-line'>
                            <h3>${book.rating}</h3>
                        </div>
                        <h1>${book.desc}</h1>
                       
                    </div>`;
            booksHTML.appendChild(newBook);
        });
    }
}


function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}

let selectedBookId;
let bookClassToAdd;

function addToList(bookId) {
    selectedBookId = bookId;
    if (!loggedIn()) {
        document.querySelector('.home').classList.add('show');
    } else {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
        var bookClass = "book" + bookId;
        bookClassToAdd = bookClass;

        console.log(bookClassToAdd);
        var inputField = document.querySelector('#myModal input[id="book"]');
        inputField.value = bookClassToAdd;
    }
}

function addToLibrary() {
    console.log(bookClassToAdd);
}


function loggedIn() {
    return (document.querySelector('.profile-dropdown') !== null);
}

const formOpenBtn = document.querySelector("#form-open"),
    home = document.querySelector(".home"),
    formContainer = document.querySelector(".form_container"),
    formCloseBtn = document.querySelector(".form_close"),
    signupBtn = document.querySelector("#signup"),
    loginBtn = document.querySelector("#login"),
    pwShowHide = document.querySelectorAll(".pw_hide");

formOpenBtn.addEventListener("click", () => home.classList.add("show"));
formCloseBtn.addEventListener("click", () => home.classList.remove("show"));

pwShowHide.forEach((icon) => {
    icon.addEventListener("click", () => {
        let getPwInput = icon.parentElement.querySelector("input");
        if (getPwInput.type === "password") {
            getPwInput.type = "text";
            icon.classList.replace("uil-eye-slash", "uil-eye");
        } else {
            getPwInput.type = "password";
            icon.classList.replace("uil-eye", "uil-eye-slash");
        }
    });
});

signupBtn.addEventListener("click", (e) => {
    e.preventDefault();
    formContainer.classList.add("active");
});
loginBtn.addEventListener("click", (e) => {
    e.preventDefault();
    formContainer.classList.remove("active");
});

$(document).ready(function () {
    $('#dimmerButton').click(function () {
        $('body').toggleClass('dimmed');
    });
});
