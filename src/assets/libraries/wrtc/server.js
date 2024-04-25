#!/usr/bin/env node
var WebSocketServer = require('websocket').server;
const app = require("express")();
var http = require('http');
var https = require('https');
const fs = require('file-system');

const SERVICE_PORT = process.env.SERVICE_PORT || 1001;

const IS_USE_SSL = process.env.IS_USE_SSL || 'No';
const SSL_KEY_FILE_PATH = process.env.SSL_KEY_FILE_PATH || '';
const SSL_CERT_FILE_PATH = process.env.SSL_CERT_FILE_PATH || '';

var clients = {};

var clients_messages = {};
var client_connections = {};

var apacheServer = http.createServer(app);

if (IS_USE_SSL.toLowerCase() === "yes") {
    const options = {
        key: fs.readFileSync(SSL_KEY_FILE_PATH),
        cert: fs.readFileSync(SSL_CERT_FILE_PATH)
    };

    apacheServer = https.createServer(options, app);
}

apacheServer.listen(SERVICE_PORT, function () {
    console.log((new Date()) + ' Server is listening on port ' + SERVICE_PORT);
});

function isJsonStr(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

wsServer = new WebSocketServer({
    httpServer: apacheServer,
    autoAcceptConnections: true,
    keepaliveInterval: 4000,
    keepaliveGracePeriod: 4000,
    closeTimeout: 4000
});

wsServer.on('connect', function (connection) {

    connection.on('message', function (message) {

        if (message.type === 'utf8') {
            let jsonData = JSON.parse(message.utf8Data);

            if (jsonData.event == "#handshake" && jsonData.data.authToken) {
                connection.authToken = jsonData.data.authToken;

                // console.log("========== Handshake "+jsonData.data.authToken+"=============");

                if (!clients[connection.authToken] || clients[connection.authToken].length < 1) {
                    clients[connection.authToken] = [];
                }

                let authToken = jsonData.data.authToken;
                clients[authToken].push(connection);

                if (clients_messages[authToken]) {

                    clients_messages[authToken].forEach(function (item_msg) {

                        clients[authToken].forEach(function (client) {
                            if (client.socket.readyState.toUpperCase() == "OPEN") {
                                client.send(item_msg);
                            }
                        });

                    });
                    delete clients_messages[authToken];
                }

            } else if (jsonData.event == "*" && jsonData.data) {
                let msgDataArr = jsonData.data.split("@@");
                let authToken = msgDataArr[0];

                // console.log("========== Data Going Sent to "+authToken+"=============");

                if (authToken && clients[authToken]) {

                    // console.log(message.utf8Data);
                    // console.log("========== Data Sent to "+authToken+" Completed =============");

                    var dataSentToClient = false;
                    clients[authToken].forEach(function (client) {
                        if (client.socket.readyState.toUpperCase() == "OPEN") {
                            client.send(message.utf8Data);
                            dataSentToClient = true;
                        }
                    });

                    if (!dataSentToClient) {

                        if (!clients_messages[authToken]) {
                            clients_messages[authToken] = [];
                        }
                        clients_messages[authToken].push(message.utf8Data);
                    }
                } else if (authToken) {

                    if (!clients_messages[authToken]) {
                        clients_messages[authToken] = [];
                    }
                    clients_messages[authToken].push(message.utf8Data);
                }

                if (msgDataArr.length > 2) {
                    let callEvent = msgDataArr[2];
                    if (callEvent.toUpperCase() == "CALLEND" && authToken && !clients_messages[authToken]) {
                        delete clients_messages[authToken];
                    } else if (isJsonStr(callEvent)) {
                        try {
                            let data_json = JSON.parse(callEvent);
                            if (data_json.type == "answer") {


                                client_connections[msgDataArr[0]] = [];
                                client_connections[msgDataArr[1]] = [];


                                client_connections[msgDataArr[0]].push(msgDataArr[1]);
                                client_connections[msgDataArr[1]].push(msgDataArr[0]);

                            }
                        } catch (e) {


                        }

                    }
                }


            }
        }

    });

});

wsServer.on('close', function (connection) {
    if (connection.authToken && clients[connection.authToken]) {
        let authToken = connection.authToken;
        clients[authToken] = clients[authToken].filter(item => item !== connection);

        if (clients[authToken].length == 0) {
            delete clients[authToken];
        }

        if (client_connections[authToken]) {
            console.log("=============");
            console.log(authToken);
            let target_user = client_connections[authToken];
            console.log(target_user);

            // let call_end_msg = "{\"event\":\"*\",\"data\":\""+target_user+"@@"+authToken+"@@CallEnd@@{\\\"Id\\\":\\\"14\\\",\\\"Name\\\":\\\"Android\\\",\\\"PImage\\\":\\\"3_1660740554_95739.jpg\\\",\\\"type\\\":\\\"Passenger\\\",\\\"isVideoCall\\\":\\\"No\\\"}\\\"}";
            let call_end_msg = "{\"event\":\"*\",\"data\":\"" + target_user + "@@" + authToken + "@@CallEnd@@ \"}";
            let call_end_msg_1 = "{\"event\":\"*\",\"data\":\"" + target_user + "@@" + target_user + "@@CallEnd@@ \"}";
            let call_end_msg_2 = "{\"event\":\"*\",\"data\":\"" + authToken + "@@" + authToken + "@@CallEnd@@ \"}";

            console.log(call_end_msg);
            console.log(call_end_msg_1);
            console.log(call_end_msg_2);

            console.log("=============");

            if (clients[target_user]) {

                var dataSentToClient = false;
                clients[target_user].forEach(function (client) {
                    if (client.socket.readyState.toUpperCase() == "OPEN") {
                        client.send(call_end_msg);
                        client.send(call_end_msg_1);
                        client.send(call_end_msg_2);
                        dataSentToClient = true;
                    }
                });

                if (!dataSentToClient) {

                    if (!clients_messages[target_user]) {
                        clients_messages[target_user] = [];
                    }
                    clients_messages[target_user].push(call_end_msg);
                    clients_messages[target_user].push(call_end_msg_1);
                    clients_messages[target_user].push(call_end_msg_2);
                }

            }


        }
    }
});