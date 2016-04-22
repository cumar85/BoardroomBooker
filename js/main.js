
function deleteBook(IsRec, href) {
    if(IsRec) {
        window.location = href + "/IsRec/" + Number(IsRec.checked);    
    } else {
        window.location = href;    
    }
    
}
function enableDisable(elem) {
    var recEnableArray = document.getElementsByClassName('enable_disable');
    var check = true;
    for (var i = 0; i < recEnableArray.length; i++) {
        if(elem.value == 1) {
            recEnableArray[i].disabled = false;
            if(recEnableArray[i].checked == true) {
                check = false;
            }
        } else {
            recEnableArray[i].disabled = true; 
        }
    }
    if(elem.value == 1 && check) {
        recEnableArray[0].checked = true;  
    }
}

function validateBookForm() {
    var note = document.getElementsByName('formData[note]')[0];
    var recurring_num = document.getElementsByName('formData[recurring_num]')[0];
    var recurringRadio = document.getElementsByName('formData[recurring]');
    for (var i = 0; i < recurringRadio.length; i++) {
        if(recurringRadio[i].checked) {
            var checkedRadioRecurring = recurringRadio[i];    
        }
    }
    var error = '';
    if(note.value === '') {
        error = 'Note must not be empty<br>';
    }
    if (+checkedRadioRecurring.value) { 
        if(recurring_num.value === '') {
            error += 'Duration must not be empty<br>';
        }else if(isNaN(recurring_num.value)) {
            error += 'Duration must be a number<br>';
        } else if(recurring_num.value <= 1 || recurring_num.value > 4 ){
            error += 'Duration must be more than 1 and less than 4<br>';    
        }
    }
    if(error == '') { 
        document.getElementById("bookform").submit();
    } else {
        var divRrror = document.getElementsByClassName('error')[0];
        var divSuccess = document.getElementsByClassName('success')[0];
        if(divSuccess !== undefined){
            divSuccess.innerHTML ='';    
        }
        divRrror.innerHTML =  error;
        window.scrollTo(0, 0);
    } 
}