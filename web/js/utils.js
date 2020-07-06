function isEmpty(v) {
    let type = typeof v; 
    if (type === 'undefined') {
        return true;
    }
    if (type === 'boolean') {
        return !v;
    }
    if (v === null) {
        return true;
    }
    if (v === undefined) {
        return true;
    }
    if (v instanceof Array) {
        if (v.length < 1) {
            return true;
        }
    } else if (type === 'string') {
        if (v.length < 1) {
            return true;
        }
        if (v === '0') {
            return true;
        }
    } else if (type === 'object') {
        if (Object.keys(v).length < 1) {
            return true;
        }
    } else if (type === 'number') {
        if (v === 0) {
            return true;
        }
        if (isNaN(v)) {
            return true;
        }
    }
    return false;
}

function appendLeadingZeroes(n){
    if(n <= 9){
      return "0" + n;
    }
    return n;
  }

function getDateWithFormat(format = 'dd/mm/yyyy', separador = "/",date = new Date())
{    
    var currentDateTime = date;    
    var formattedDate=currentDateTime.toString();

    if(format == 'dd/mm/yyyy' || format == 'dd-mm-yyyy'){
        formattedDate = appendLeadingZeroes(currentDateTime.getDate()) + separador + 
                        appendLeadingZeroes(currentDateTime.getMonth() + 1) + separador + 
                        currentDateTime.getFullYear();
    }

    if(format == 'dd/mm/yyyy hh:mm:ss' || format == 'dd-mm-yyyy hh:mm:ss'){
        formattedDate = appendLeadingZeroes(currentDateTime.getDate()) + separador + 
                        appendLeadingZeroes(currentDateTime.getMonth() + 1) + separador + 
                        currentDateTime.getFullYear()+ " " + 
                        currentDateTime.getHours() + ":" + 
                        currentDateTime.getMinutes() + ":" + 
                        currentDateTime.getSeconds();
    }

    if(format == 'yyyy/mm/dd' || format == 'yyyy-mm-dd'){
        formattedDate = currentDateTime.getFullYear() + separador + 
                        appendLeadingZeroes(currentDateTime.getMonth() + 1) + separador + 
                        appendLeadingZeroes(currentDateTime.getDate());
    }

    if(format == 'yyyy-mm-dd hh:mm:ss' || format == 'yyyy/mm/dd hh:mm:ss'){        
        formattedDate = current_datetime.getFullYear() + separador + 
                                (currentDateTime.getMonth() + 1) + separador + 
                                currentDateTime.getDate() + " " + 
                                currentDateTime.getHours() + ":" + 
                                currentDateTime.getMinutes() + ":" + 
                                currentDateTime.getSeconds()         
    }
    console.log("desde ",formattedDate);
    return formattedDate;
}