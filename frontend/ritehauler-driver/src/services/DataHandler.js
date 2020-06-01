let callback;
let lastScene;
class DataHandler {
  setCallback(cb) {
    callback = cb;
  }

  callback(reset = false) {
    if (callback) {
      callback();
      if (reset) {
        callback = undefined;
      }
    }
  }

  setLastScene(ls) {
    lastScene = ls;
    callback = undefined;
  }

  lastScene() {
    return lastScene;
  }
}

export default new DataHandler();
