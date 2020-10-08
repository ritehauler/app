let isFirstItem = false;
class DataHandler {
  setFirstItem(isFirst) {
    isFirstItem = isFirst;
  }

  isFirstItem() {
    return isFirstItem;
  }
}

export default new DataHandler();
