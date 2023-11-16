function buyItem(productId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/app/functions/buy.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Toastify({
                        text: "Successful purchase",
                        duration: 3000
                    }).showToast();
                } else if (response.auth) {
                    Toastify({
                        text: "Authentication error",
                        duration: 3000
                    }).showToast();
                } else {
                    Toastify({
                        text: "Insufficient funds",
                        duration: 3000
                    }).showToast();
                }
            } else {
                Toastify({
                    text: "An error occurred",
                    duration: 3000
                }).showToast();
            }
        }
    };
    xhr.send("itemId=" + productId);
}
console.log('JDonate 2023 | // https://github.com/DevJaehaerys/jdonate')
function submitForm() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);

    fetch('/app/payments/freekassa/red.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.redirectUrl) {
                window.location.href = data.redirectUrl;
            } else {
                console.error('ops');
            }
        })
        .catch(error => {
            alert('Enter number >= 10')
        });
}
document.getElementById('activatePromoButton').addEventListener('click', function() {
    var promocode = prompt('Promocode:');

    fetch('/app/functions/promo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
            body: JSON.stringify({ promocode: promocode }),
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('500');
        });
});
