

function isEmpty(string) {
    string = string.replace(/\s+/g, '');
    if (string == null || string == '') {
        return true;
    }
    return false; 
}

function validateEntry() {
    console.log('Validating');
    try {
        fn = document.getElementById('fn').value;
        ln = document.getElementById('ln').value;
        em = document.getElementById('em').value;
        hd = document.getElementById('hd').value;
        sm = document.getElementById('sm').value;
        if (isEmpty(fn) || isEmpty(ln) || isEmpty(em) || isEmpty(hd) || isEmpty(sm)) {
            alert("All fields are required");
            return false;
        } 
        if (em.includes('@')) {
            return true; 
        } else {
            alert('Invalid email adress');
        }
    } catch (error) {
        console.log(error); 
        return false; 
    }
    return false; 
}