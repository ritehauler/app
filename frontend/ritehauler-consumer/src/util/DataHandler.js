let isFirstItem = false;
let isChangeNumber = false;
let callBackOrderPlace;
let callBackOrderDiscard;
let currency = "$";
let expensiveItemCost = 100;
let isOpenFromNotification = false;
let callBackNotification;
let callBackOrderDetail;
let callBackTrackOrder;
let store;
class DataHandler {
  setFirstItem(isFirst) {
    isFirstItem = isFirst;
  }

  isFirstItem() {
    return isFirstItem;
  }

  setIsChangeNumber(isChange) {
    isChangeNumber = isChange;
  }

  isChangeNumber() {
    return isChangeNumber;
  }

  setCallBackOrderPlace(cb) {
    callBackOrderPlace = cb;
  }

  callBackOrderPlace() {
    if (callBackOrderPlace) {
      callBackOrderPlace();
    }
  }

  setCallBackOrderDetail(cb) {
    callBackOrderDetail = cb;
  }

  callBackOrderDetail(entity_history_id, order_id, order_number) {
    if (callBackOrderDetail) {
      callBackOrderDetail(entity_history_id, order_id, order_number);
    }
  }

  setCallBackTrackOrder(cb) {
    callBackTrackOrder = cb;
  }

  callBackTrackOrder(entity_history_id, order_id, order_number) {
    if (callBackTrackOrder) {
      callBackTrackOrder(entity_history_id, order_id, order_number);
    }
  }

  isTrackOrderCallbackIsSet() {
    if (callBackTrackOrder) {
      return true;
    }
    return false;
  }

  isOderDetailCallbackIsSet() {
    if (callBackOrderDetail) {
      return true;
    }
    return false;
  }

  setCallBackOrderDiscard(cb) {
    callBackOrderDiscard = cb;
  }

  callBackNotification() {
    if (callBackNotification) {
      callBackNotification();
    }
  }

  setCallBackNotification(cb) {
    callBackNotification = cb;
  }

  callBackOrderDiscard() {
    if (callBackOrderDiscard) {
      callBackOrderDiscard();
    }
  }

  setCurrency(cur) {
    currency = cur;
  }

  currency() {
    return currency;
  }

  setExpensiveItemCost(cost) {
    expensiveItemCost = cost;
  }

  expensiveItemCost() {
    return expensiveItemCost;
  }
  setIsOpenFromNotification(isOpen) {
    isOpenFromNotification = isOpen;
  }

  isOpenFromNotification() {
    return isOpenFromNotification;
  }
  setStore(sto) {
    store = sto;
  }

  getStore() {
    return store;
  }
}

export default new DataHandler();
