class SCClient {
    constructor() {
        if (!this.SOCKET) {

            let protocol = TSITE_SC_PROTOCOL;

            this.SOCKET = socketClusterClient.create({
                hostname: TSITE_SC_HOST,
                path: TSITE_HOST_SC_PATH,
                port: TSITE_HOST_SC_PORT,
                secure: protocol.startsWith("https://") ? true : false
            });

        }
    }

    subscribe(channel_name, callback) {
        (async () => {
            let subscribed_channel = this.SOCKET.subscribe(channel_name);

            await subscribed_channel.listener('subscribe').once();

            for await (let data of subscribed_channel) {
                callback(data);
            }
        })();
    }

    publish(channel_name, message) {
        this.SOCKET.transmitPublish(channel_name, message);
    }
}

let SOCKET_OBJ = new SCClient();