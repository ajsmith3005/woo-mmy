//Enable make and model select elements
const makeModelYearForm = document.getElementById("make-model-year-form");
const yearDropdown = document.getElementById("car-year");
const makeDropdown = document.getElementById("make");
const modelDropdown = document.getElementById("model");

//Performs following actions if the make/model/year form is present on page
if ( makeModelYearForm ) {

    // Enables model selection field on change and performs REST API call to retrieve model selection options
    makeDropdown.addEventListener("change", (event) => {
        modelDropdown.selectedIndex = 0;
        yearDropdown.setAttribute("disabled", "");
        yearDropdown.selectedIndex = 0;

        if(makeDropdown.selectedIndex === 0) {
            modelDropdown.setAttribute("disabled", "");
        } else{
            modelDropdown.removeAttribute("disabled");
            let makeValue = makeDropdown.value;
            let modelOptions = '<option value="">Model</option>'
            modelDropdown.innerHTML = modelOptions;
            fetch('/wp-json/woommy/v1/models?selected_make=' + makeValue + `&timestamp=${Date.now()}`)
            .then(response => response.json())
            .then(models => {
                models.forEach(model => {
                    modelOptions += '<option value="' + model.slug + '">' + model.name + '</option>'
                });
                modelDropdown.innerHTML = modelOptions;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }
    });

    // Enables year selection field on change and performs REST API call to retrieve year selection options
    modelDropdown.addEventListener("change", (event) => {        
        
        yearDropdown.selectedIndex = 0;

        if (modelDropdown.selectedIndex === 0) {
            yearDropdown.setAttribute("disabled", "");
        } else {
            yearDropdown.removeAttribute("disabled");
            let modelValue = modelDropdown.value;
            let yearOptions = '<option value="">Year</option>'
            yearDropdown.innerHTML = yearOptions;
            fetch('/wp-json/woommy/v1/years?selected_model=' + modelValue + `&timestamp=${Date.now()}`)
            .then(response => response.json())
            .then(years => {
                years.forEach( year => {
                    yearOptions += '<option value="' + year.slug + '">' + year.name + '</option>'
                });
                yearDropdown.innerHTML = yearOptions;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }
    });
}
