function toggleSelectBox() {
        const selectBox = document.querySelector('.nested-select-box');
        if (selectBox) {
            selectBox.classList.toggle('show');
            console.log("Toggle select box visibility.");
        } else {
            console.error("Could not find the select box element.");
        }
    }

    document.addEventListener('click', function (event) {
        const selectBox = document.querySelector('.nested-select-box');
        if (selectBox && !event.target.closest('.nested-select')) {
            selectBox.classList.remove('show');
            console.log("Clicked outside, hide select box.");
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const selectButton = document.querySelector('.nested-select-button');
        if (selectButton) {
            selectButton.addEventListener('click', function (event) {
                event.stopPropagation();
                console.log("Clicked on select button.");
            });
        } else {
            console.error("Could not find the select button element.");
        }
    });