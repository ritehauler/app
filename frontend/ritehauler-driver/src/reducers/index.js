import { combineReducers } from "redux";
import { LOGOUT } from "../actions/ActionTypes";
import navigator from "./navigator";
import user from "./user";
import location from "./location";
import updateAttachment from "./updateAttachment";
import itemNames from "./itemNames";
import networkInfo from "./networkInfo";
import itemBox from "./itemBox";
import orderInfo from "./orderInfo";
import myOrders from "./myOrders";
import assignedOrders from "./assignedOrders";
import updateAssignedOrderStatus from "./updateAssignedOrderStatus";
import addExtraItem from "./addExtraItem";
import orderItems from "./orderItems";
import verifyVolume from "./verifyVolume";
import detailCard from "./detailCard";
import handleMap from "./handleMap";
import todaysOrders from "./todaysOrders";
import orderDetails from "./orderDetails";
import mapRoute from "./mapRoute";
import pendingOrders from "./pendingOrders";
import ratingsList from "./ratingsList";
import submitRatings from "./submitRatings";
import statistics from "./statistics";
import weeklyStatistics from "./weeklyStatistics";
import orderStatuses from "./orderStatuses";
import generalSettings from "./generalSettings";
import cmsContent from "./cmsContent";
import notificationListing from "./notificationListing";

const appReducer = combineReducers({
  route: navigator,
  user: user,
  location: location,
  networkInfo: networkInfo,
  updateAttachment: updateAttachment,
  itemNames: itemNames,
  itemBox: itemBox,
  orderInfo: orderInfo,
  myOrders: myOrders,
  assignedOrders: assignedOrders,
  updateAssignedOrderStatus: updateAssignedOrderStatus,
  addExtraItem: addExtraItem,
  orderItems: orderItems,
  verifyVolume: verifyVolume,
  detailCard: detailCard,
  handleMap: handleMap,
  todaysOrders: todaysOrders,
  orderDetails: orderDetails,
  mapRoute: mapRoute,
  pendingOrders: pendingOrders,
  ratingsList: ratingsList,
  submitRatings: submitRatings,
  statistics: statistics,
  weeklyStatistics: weeklyStatistics,
  orderStatuses: orderStatuses,
  generalSettings: generalSettings,
  cmsContent: cmsContent,
  notificationListing: notificationListing
});

export default (rootReducer = (state, action) => {
  //if (action.type === LOGOUT.SUCCESS) {
  if (action.type === LOGOUT.SUCCESS) {
    //const { networkInfo } = state;
    //state = { networkInfo };
    state = undefined;
  }

  return appReducer(state, action);
});
