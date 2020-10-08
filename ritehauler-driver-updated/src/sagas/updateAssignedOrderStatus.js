import { put, call, take, fork, select } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";
import _ from "lodash";
import ApiSauce from "../services/ApiSauce";
import {
  API_UPDATE_ASSIGNED_ORDER_STATUS,
  GOOGLE_API_KEY,
  API_GOOGLE_DIRECTION
} from "../config/WebService";
import {
  STATUS_ARRIVED,
  STATUS_ON_THE_WAY,
  STATUS_REACHED,
  STATUS_ACCEPTED,
  STATUS_DECLINED,
  ENTITY_TYPE_ID_MY_ORDERS,
  ENTITY_TYPE_ORDER_ITEM,
  RIDE_MODE
} from "../constant";
import { orderSummary, verifyItems, createOrder } from "../navigator/Keys";
import Utils from "../util";

// redux imports
import { success, failure } from "../actions/UpdateAssignedOrderStatus";
import { focusMap } from "../actions/HandleMapActions";
import { success as todaysOrdersSuccess } from "../actions/TodaysOrdersActions";
import { success as assignedOrdersSuccess } from "../actions/AssignedOrders";
import {
  startBackgroundLocationService,
  stopBackgroundLocationService,
  setTrackingStatus
} from "../actions/LocationActions";
import { UPDATE_ASSIGNED_ORDER_STATUS } from "../actions/ActionTypes";
import { request as mapRouteRequest } from "../actions/MapRouteActions";
import { hideCard } from "../actions/DetailCardActions";
import { getTodaysOrders, getAssignedOrders } from "../reducers/selectors";
import { request as generalSettingsRequest } from "../actions/GeneralSettingsActions";

function callRequest(data) {
  return ApiSauce.post(API_UPDATE_ASSIGNED_ORDER_STATUS, data);
}

function updateOrderStatus(data, status) {
  let tempData = _.cloneDeep(data);
  tempData[0].order_status.value = status;
  return tempData;
}

function removeTopMostAssignedOrder(data) {
  let tempData = _.cloneDeep(data);
  tempData.splice(0, 1);
  return tempData;
}

function* watchUserRequest() {
  while (true) {
    const { payload } = yield take(UPDATE_ASSIGNED_ORDER_STATUS.REQUEST);
    try {
      const response = yield call(callRequest, payload);

      // if its neither accepted nor declined then that means driver is processing current order
      // so change order status from todays orders reducers
      if (
        payload.order_status !== STATUS_DECLINED &&
        payload.order_status !== STATUS_ACCEPTED &&
        response.data.order_history.order_status.value
      ) {
        const todaysOrders = yield select(getTodaysOrders);
        const alteredTodaysOrders = yield call(
          updateOrderStatus,
          todaysOrders,
          response.data.order_history.order_status.value
        );
        yield put(todaysOrdersSuccess(alteredTodaysOrders));
      }

      yield put(success(response.data.order_history || {}));

      // if i have accepted an order and there are multiple order to be accepted
      // then remove the top most successfully accepted order and show the second one
      if (
        payload.order_status === STATUS_ACCEPTED ||
        payload.order_status === STATUS_DECLINED
      ) {
        const assignedOrders = yield select(getAssignedOrders);
        if (assignedOrders.length > 1) {
          const alteredAssignedOrders = yield call(
            removeTopMostAssignedOrder,
            assignedOrders
          );
          yield put(assignedOrdersSuccess(alteredAssignedOrders));
          yield put(
            mapRouteRequest(API_GOOGLE_DIRECTION, {
              key: GOOGLE_API_KEY,
              mode: RIDE_MODE.DRIVING,
              origin: Utils.getLocationString(
                alteredAssignedOrders[0].order_pickup[0]
              ),
              destination: Utils.getLocationString(
                alteredAssignedOrders[0].order_dropoff[0]
              )
            })
          );
          // decline can happen on order assignment screen so we cant pop from here if multiple assigned order
          if (Actions.currentScene === orderSummary) {
            Actions.pop();
          }
        } else {
          /* making a call for general settings to get new pending orders count */

          yield put(
            generalSettingsRequest({
              driver_id: payload.driver_id
            })
          );

          if (Actions.currentScene === orderSummary) {
            // pop twice once from order summary and then from order assignment
            // since user can be any where in app and we cant just navigate him to home
            Actions.pop();
            Actions.pop();
          } else {
            // when declined from order delivery flow
            if (
              Actions.currentScene === verifyItems ||
              Actions.currentScene === createOrder
            ) {
              Actions.reset("home");
            } else {
              // when declined from order assignment flow
              Actions.pop();
            }
          }
        }
      }

      if (payload.order_status === STATUS_ARRIVED) {
        Actions.verifyItems();
      }
      // when validating from verify items screen
      if (payload.order_status === STATUS_ON_THE_WAY && !payload.home) {
        yield put(focusMap(true));
        yield put(startBackgroundLocationService());
        yield put(setTrackingStatus(true));
        Actions.pop();
      }
      // when validating from create order
      if (payload.order_status === STATUS_ON_THE_WAY && payload.home) {
        yield put(startBackgroundLocationService());
        yield put(setTrackingStatus(true));
        Actions.reset("home");
        // resetting create order lock
        Utils.createOrderLock(false);
      }
      if (payload.order_status === STATUS_REACHED) {
        yield put(hideCard());
        yield put(stopBackgroundLocationService());
        yield put(setTrackingStatus(false));

        // fetch current order and display its order id
        const todaysOrders = yield select(getTodaysOrders);

        const orderID =
          todaysOrders.length && todaysOrders[0].order_number
            ? todaysOrders[0].order_number
            : "Order Receipt";

        Actions.orderReceipt({ title: orderID });

        yield put(
          generalSettingsRequest({
            driver_id: payload.driver_id
          })
        );
      }
    } catch (err) {
      yield put(failure(err.message));
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
}
