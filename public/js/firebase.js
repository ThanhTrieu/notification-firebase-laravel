/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/
var firebaseConfig = {
    apiKey: "AIzaSyDws1ujQJY3sc12mV8TTc3zYjdXeh-arGo",
    authDomain: "send-notification-63cf4.firebaseapp.com",
    projectId: "send-notification-63cf4",
    storageBucket: "send-notification-63cf4.appspot.com",
    messagingSenderId: "674599085319",
    appId: "1:674599085319:web:78c5b4bddc2f8f44c1d1a2",
    measurementId: "G-LVSERJS7YS"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

/**
 * We can start messaging using messaging() service with firebase object
 */
var messaging = firebase.messaging();

/** Register your service worker here
 *  It starts listening to incoming push notifications from here
 */
navigator.serviceWorker.register('./firebase-messaging-sw.js')
.then(function (registration) {
    /** Since we are using our own service worker ie firebase-messaging-sw.js file */
    messaging.useServiceWorker(registration);

    /** Lets request user whether we need to send the notifications or not */
    messaging.requestPermission()
        .then(function () {
            /** Standard function to get the token */
            messaging.getToken()
            .then(function(token) {
                /** Here I am logging to my console. This token I will use for testing with PHP Notification */
                //console.log(token);
                /** SAVE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
                var xhttp = new XMLHttpRequest();
                var tokenAjax = document.querySelector('meta[name="csrf-token"]').content;
                xhttp.open("POST", urlAjaxSaveTokenFirebase, true);
                xhttp.setRequestHeader('X-CSRF-TOKEN', tokenAjax); 
                xhttp.setRequestHeader("Content-Type", "application/json");
                xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Response
                    var response = this.responseText;
                    console.log(response);
                }
                };
                var data = {token: token};
                xhttp.send(JSON.stringify(data));
            })
            .catch(function(error) {
                /** If some error happens while fetching the token then handle here */
                updateUIForPushPermissionRequired();
                console.log('Error while fetching the token ' + error);
            });
        })
        .catch(function (error) {
            /** If user denies then handle something here */
            console.log('Permission denied ' + error);
        })
})
.catch(function () {
    console.log('Error in registering service worker');
});

/** What we need to do when the existing token refreshes for a user */
messaging.onTokenRefresh(function() {
    messaging.getToken()
    .then(function(renewedToken) {
        console.log(renewedToken);
        /** UPDATE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
    })
    .catch(function(error) {
        /** If some error happens while fetching the token then handle here */
        console.log('Error in fetching refreshed token ' + error);
    });
});

// Handle incoming messages
messaging.onMessage(function(payload) {
    const notificationTitle = 'Data Message Title';
    const notificationOptions = {
        body: 'Data Message body',
        icon: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg',
        image: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg'
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});