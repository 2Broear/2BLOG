
self.onmessage = async event=> {
    const { img } = event.data;
    try {
        let blob;
        if (typeof fetch === 'function') {
            // 使用 Fetch API 加载图片
            const response = await fetch(img);
            // 使用 FileReader 传输 DataURL(faster?)
            blob = await response.blob();
            self.postMessage({ url: URL.createObjectURL(blob) });
        }else{
            // 使用 XMLHttpRequest 加载图片
            const xhr = new XMLHttpRequest();
            xhr.open('GET', img, true);
            xhr.responseType = 'arraybuffer';
            xhr.onload = function() {
                const arrayBuffer = xhr.response;
                blob = new Blob([arrayBuffer]);
                blobUrl =  URL.createObjectURL(blob);
                self.postMessage({ url: blobUrl });
            };
            xhr.send();
        }
    } catch (error) {
        console.error('Error converting img2rgb:', error);
    }
};
