import { fork } from "redux-saga/effects";
import init from "./init";
import user from "./user";
import attachment from "./attachment";
import itemNames from "./itemNames";
import itemBox from "./itemBox";
import myOrders from "./myOrders";
import assignedOrders from "./assignedOrders";
import updateAssignedOrderStatus from "./updateAssignedOrderStatus";
import addExtraItem from "./addExtraItem";
import orderItems from "./orderItems";
import verifyVolume from "./verifyVolume";
import todaysOrders from "./todaysOrders";
import orderDetails from "./orderDetails";
import mapRoute from "./mapRoute";
import pendingOrders from "./pendingOrders";
import ratingsList from "./ratingsList";
import submitRatings from "./submitRatings";
import orderDriverTracking from "./orderDriverTracking";
import statistics from "./statistics";
import weeklyStatistics from "./weeklyStatistics";
import orderStatuses from "./orderStatuses";
import dutyToggle from "./dutyToggle";
import generalSettings from "./generalSettings";
import cmsContent from "./cmsContent";
import notificationListing from "./notificationListing";

export default function* root() {
  yield fork(init);
  yield fork(user);
  yield fork(attachment);
  yield fork(itemNames);
  yield fork(itemBox);
  yield fork(myOrders);
  yield fork(assignedOrders);
  yield fork(updateAssignedOrderStatus);
  yield fork(addExtraItem);
  yield fork(orderItems);
  yield fork(verifyVolume);
  yield fork(todaysOrders);
  yield fork(orderDetails);
  yield fork(mapRoute);
  yield fork(pendingOrders);
  yield fork(ratingsList);
  yield fork(submitRatings);
  yield fork(orderDriverTracking);
  yield fork(statistics);
  yield fork(weeklyStatistics);
  yield fork(orderStatuses);
  yield fork(dutyToggle);
  yield fork(generalSettings);
  yield fork(cmsContent);
  yield fork(notificationListing);
}
