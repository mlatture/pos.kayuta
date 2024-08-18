// Common Variables
const tRow = document.querySelector(".t__head");

const dragabbles = document.querySelectorAll(".dragabble");
const containers = document.querySelectorAll("tbody tr");
const currMonthTd = document.querySelector(".curr__month");
const searchToggle = document.querySelector('.search__toggle')
const searchField = document.querySelector('.search__form')
//************* Dates Stuff *************//
const days = ["Sun", "Mon", "Tues", "Wed", "Thu", "Fri", "Sat"];
// const monthsName = [
//     "Januarary",
//     "Feburary",
//     "March",
//     "April",
//     "May",
//     "June",
//     "July",
//     "August",
//     "September",
//     "October",
//     "November",
//     "December",
// ];
// Getting current mont, year, days
// const date = new Date();
// const months = date.getMonth();
// const getDate = date.getDate();
// const getDay = date.getDay();
// const currentYear = date.getFullYear();
// const currentMonth = monthsName[months];
// const currentDay = days[getDay];
// const totalDaysInMonth = daysInMonth(months, currentYear);

// currMonthTd.innerHTML = `${currentMonth}<br/>${currentYear}`

// Get number of days
// function daysInMonth(month, year) {
//     return new Date(year, month + 1, 0).getDate();
// }

// Appending the days and dates in table row
// for (let day = 1; day <= totalDaysInMonth; day++) {
//     const currentDate = new Date(currentYear, months, day);
//     const dayOfWeek = currentDate.getDay();
//     let tHead = document.createElement("th");
//     tRow.appendChild(
//         tHead
//     ).innerHTML = `${days[dayOfWeek]} <hr class="m-0"/> ${day}`;
// }

//*********************** Date Range functionalit ***********************y

const dateRangePicker = $('input[name="daterange"]').daterangepicker();
$(function () {
    $('input[name="daterange"]').daterangepicker(
        {
            opens: "left",
        },
        $("#submitRange").on("click", function () {
            const startDate = $('input[name="daterange"]').data(
                "daterangepicker"
            ).startDate;
            const endDate = $('input[name="daterange"]').data(
                "daterangepicker"
            ).endDate;
            updateTableHeader(startDate, endDate);
        })
    );
});
function updateTableHeader(startDate, endDate) {
    let selectedDates = [];
    // const start = startDate.clone();
    // const end = endDate.clone();
    // while (start.isSameOrBefore(end, "day")) {
    //     start.add(1, "day");
    //     selectedDates.push(start.format("YYYY-MM-DD"));
    // }
    // console.log(selectedDates, "selected dates");
    // return selectedDates;
    const start = startDate.format("YYYY-MM-DD");
    const end = endDate.format("YYYY-MM-DD");
    selectedDates = [start, end];
    console.log(selectedDates)
    return selectedDates;
}

// Un--Used Code
// function updateTableHeader(startDate, endDate) {
// const dateTheads = document.querySelectorAll(".t__head th");
// dateTheads.forEach((th) => {
//     th.remove();
// });
// const start = startDate.clone();
// const end = endDate.clone();
// while (start.isSameOrBefore(end, "day")) {
//     const dayOfWeek = start.day();
//     const dayOfMonth = start.date();
//     let th = document.createElement("th");
//     th.innerHTML = `${days[dayOfWeek]} <hr class="m-0"/> ${dayOfMonth}`;
//     tRow.appendChild(th);
//     start.add(1, "day");
// }
// }
//*********************** Date Range functionalit ***********************//

//************* Dates Stuff *************//

//*************** Draggabble Functionality ***************//

document.addEventListener("dragstart", (e) => {
    const dragabble = e.target.closest(".dragabble");
    if (dragabble) {
        dragabble.classList.add("draging");
    }
});

document.addEventListener("dragend", (e) => {
    const dragabble = e.target.closest(".dragabble");
    if (dragabble) {
        dragabble.classList.remove("draging");
    }
});

containers.forEach((container) => {
    container.addEventListener("dragover", (e) => {
        e.preventDefault();
        const draggable = document.querySelector(".draging");
        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement === null) {
            container.appendChild(draggable);
        } else {
            container.insertBefore(draggable, afterElement);
        }
    });
});

function getDragAfterElement(container, y) {
    const draggableElements = [
        ...container.querySelectorAll(".dragabble:not(.draging)"),
    ];
    return draggableElements.reduce(
        (closestElement, currentElement) => {
            const box = currentElement.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (
                offset < 0 &&
                Math.abs(offset) < Math.abs(closestElement.offset)
            ) {
                return { offset: offset, element: currentElement };
            } else {
                return closestElement;
            }
        },
        { offset: Infinity }
    ).element;
}

//*************** Draggabble Functionality ***************//


//*************** search fieldd ***************//
searchToggle.addEventListener('click', ()=> {

    searchField.classList.toggle('show')
    if(searchField.classList.contains('show')){
        searchToggle.innerHTML = `<i class="fa fa-close"></i>`
    } else {
        searchToggle.innerHTML = `<i class="fa fa-search"></i>`

    }
})
//*************** search fieldd ***************//



