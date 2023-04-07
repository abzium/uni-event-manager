const urlBase = 'http://134.209.161.200/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";
let userLevel = 0;
let userDomain = "";

function doLogin() {
	userId = 0;
	firstName = "";
	lastName = "";
	userLevel = 0;

	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	//	var hash = md5( password );

	document.getElementById("loginResult").innerHTML = "";

	let tmp = { email: login, password: password };
	//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);
				userId = jsonObject.userID;

				if (userId < 1) {
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}

				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;
				userLevel = jsonObject.userLevel;
				// extract domain
				let tokens = email.split("@").join(".").split(".");
				userDomain = tokens[tokens.length - 2] + "." + tokens[tokens.length - 1];

				saveCookie();

				if (userLevel == 1) {
					window.location.href = "adminPage.html";
				}
				else {
					window.location.href = "color.html";
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function doRegister() {
	let firstName = document.getElementById("registerFirstName").value;
	let lastName = document.getElementById("registerLastName").value;
	let email = document.getElementById("registerEmail").value;
	let password = document.getElementById("registerPassword").value;

	// extract domain
	let tokens = email.split("@").join(".").split(".");
	let domain = tokens[tokens.length - 2] + "." + tokens[tokens.length - 1];

	let tmp = { firstName: firstName, lastName: lastName, email: email, domain: domain, password: password };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/Register.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);
				userId = jsonObject.userID;

				if (userId < 1) {
					document.getElementById("registerResult").innerHTML = jsonObject.error;
					return;
				}

				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;
				userLevel = jsonObject.userLevel;

				document.getElementById("registerResult").innerHTML = "Register successful!";


			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("registerResult").innerHTML = err.message;
	}

}

function saveCookie() {
	let minutes = 40;
	let date = new Date();
	date.setTime(date.getTime() + (minutes * 60 * 1000));
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ",userLevel=" + userLevel + ",userDomain=" + userDomain + ";expires=" + date.toGMTString();
}

function readCookie() {
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for (var i = 0; i < splits.length; i++) {
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if (tokens[0] == "firstName") {
			firstName = tokens[1];
		}
		else if (tokens[0] == "lastName") {
			lastName = tokens[1];
		}
		else if (tokens[0] == "userId") {
			userId = parseInt(tokens[1].trim());
		}
		else if (tokens[0] == "userLevel") {
			userLevel = parseInt(tokens[1].trim());
		}
	}

	if (userId < 0) {
		window.location.href = "index.html";
	}
	else {
		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout() {
	userId = 0;
	firstName = "";
	lastName = "";
	userLevel = 0;
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addColor() {
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = { color: newColor, userId, userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/AddColor.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("colorAddResult").innerHTML = err.message;
	}

}

function searchColor() {
	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";

	let colorList = "";

	let tmp = { search: srch, userId: userId };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/SearchColors.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse(xhr.responseText);

				for (let i = 0; i < jsonObject.results.length; i++) {
					colorList += jsonObject.results[i];
					if (i < jsonObject.results.length - 1) {
						colorList += "<br />\r\n";
					}
				}

				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}

}

function changeAdminForm() {
	let value = document.getElementById('createOption').value;
	let rsoForm = document.getElementById('rsoInfo');
	let eventForm = document.getElementById('eventInfo');
	let rsoEvForm = document.getElementById('rsoEvInfo');
	let submit = document.getElementById('submitButton');
	document.getElementById('sbtndiv').style.display = "block";

	if (value == "createRSO") {
		rsoForm.style.display = "block";
		eventForm.style.display = "none";
		rsoEvForm.style.display = "none";
		submit.setAttribute("onclick", "createRSO();")

	} else if (value == "createPriEv") {
		rsoForm.style.display = "none";
		eventForm.style.display = "block";
		rsoEvForm.style.display = "none";
		submit.setAttribute("onclick", "createPrivateEvent();")

	} else if (value == "createPubEv") {
		rsoForm.style.display = "none";
		eventForm.style.display = "block";
		rsoEvForm.style.display = "none";
		submit.setAttribute("onclick", "createPublicEvent();")

	} else {
		// create RSO event
		rsoForm.style.display = "none";
		eventForm.style.display = "block";
		rsoEvForm.style.display = "block";
		submit.setAttribute("onclick", "createRSOEvent();")
	}
}

function createRSO() {
	document.getElementById("createResult").innerHTML = "";
	let rsoName = document.getElementById("rsoName").value;

	let tmp = { adminID: userId, name: rsoName, domain: userDomain };
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/CreateRSO.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("createResult").innerHTML = "RSO has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("createResult").innerHTML = err.message;
	}
}

function createPrivateEvent() {
	document.getElementById("createResult").innerHTML = "";

	let eventName = document.getElementById("eventName").value;
	let catName = document.getElementById("catName").value;
	let descr = document.getElementById("descr").value;
	let phone = document.getElementById("phone").value;
	let email = document.getElementById("email").value;


	let tmp = {
		name: eventName,
		category: catName,
		description: descr,
		contactPhone: phone,
		contactEmail: email,
		timestamp: 0,
		adminID: userId,
		uniID: 3
	};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/CreateRSO.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("createResult").innerHTML = "RSO has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("createResult").innerHTML = err.message;
	}
}

function createPublicEvent() {
	console.log("cpub");
}

function createRSOEvent() {
	console.log("crev");
}