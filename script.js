function showFeedback() {

    let name = document.getElementById("name").value;
    let regno = document.getElementById("regno").value;
    let semester = document.getElementById("semester").value;

    if(name === "" || regno === "" || semester === "") {
        alert("Please fill all student details");
        return;
    }

    document.getElementById("hiddenName").value = name;
    document.getElementById("hiddenReg").value = regno;
    document.getElementById("hiddenSem").value = semester;

    document.getElementById("studentForm").style.display = "none";
    document.getElementById("feedbackForm").style.display = "block";
}
