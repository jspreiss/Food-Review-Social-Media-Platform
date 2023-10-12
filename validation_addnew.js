const validation_addnew = new JustValidate("#addnew");

validation_addnew
    .addField("#name", [
        {
            rule: "required"
        },
        {
            validator: (value) => () => {
                return fetch("validate-addnew.php?name=" + encodeURIComponent(value))
                       .then(function(response) {
                           return response.json();
                       })
                       .then(function(json) {
                           return json.available;
                       });
            },
            errorMessage: "Fruit already exists"
        },
        {
            validator: (value) => () => {
                return fetch("validate-addnew-fruit.php?name=" + encodeURIComponent(value))
                       .then(function(response) {
                           return response.json();
                       })
                       .then(function(json) {
                           return json.available;
                       });
            },
            errorMessage: "Not a fruit"
        }
    ])
    .onSuccess((event) => {
        document.getElementById("addnew").submit();
    });