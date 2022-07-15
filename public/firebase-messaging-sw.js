/** Again import google libraries */
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js");

/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/
var config = {
    apiKey: "AIzaSyDws1ujQJY3sc12mV8TTc3zYjdXeh-arGo",
    authDomain: "send-notification-63cf4.firebaseapp.com",
    projectId: "send-notification-63cf4",
    storageBucket: "send-notification-63cf4.appspot.com",
    messagingSenderId: "674599085319",
    appId: "1:674599085319:web:78c5b4bddc2f8f44c1d1a2",
    measurementId: "G-LVSERJS7YS"
};
firebase.initializeApp(config);

// Retrieve an instance of Firebase Data Messaging so that it can handle background messages.
const messaging = firebase.messaging();

/** THIS IS THE MAIN WHICH LISTENS IN BACKGROUND */
messaging.setBackgroundMessageHandler(function(payload) {
    const notificationTitle = 'BACKGROUND MESSAGE TITLE';
    const notificationOptions = {
        body: 'Data Message body',
        icon: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg',
        image: 'https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg'
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});