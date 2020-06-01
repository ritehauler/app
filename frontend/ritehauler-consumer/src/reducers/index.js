import { combineReducers } from "redux";

import user from "./user";
import itemNames from "./itemNames";
import networkInfo from "./networkInfo";
import updateAttachment from "./updateAttachment";
import itemBox from "./itemBox";
import orderInfo from "./orderInfo";
import suggestedVehicles from "./suggestedVehicles";
import deliveryProfessionals from "./deliveryProfessionals";
import saveVehicle from "./saveVehicle";
import saveProfessional from "./saveProfessional";
import orderPlace from "./orderPlace";
import logout from "./logout";
import googleNearBy from "./googleNearBy";
import googlePlaces from "./googlePlaces";
import stateCity from "./stateCity";
import recentLocations from "./recentLocations";
import orderListing from "./orderListing";
import orderDetail from "./orderDetail";
import ratingOptions from "./ratingOptions";
import rateDriver from "./rateDriver";
import driverProfile from "./driverProfile";
import notificationListing from "./notificationListing";
import generalSettings from "./generalSettings";
import cmsContent from "./cmsContent";
import notificationInfo from "./notificationInfo";
import orderComplete from "./orderComplete";
import trackOrder from "./trackOrder";
import cards from "./cards";
import chargeCard from "./chargeCard";

export default combineReducers({
  user,
  itemNames,
  networkInfo,
  updateAttachment,
  orderListing,
  itemBox,
  orderDetail,
  ratingOptions,
  orderInfo,
  suggestedVehicles,
  deliveryProfessionals,
  saveVehicle,
  saveProfessional,
  orderPlace,
  logout,
  googleNearBy,
  googlePlaces,
  stateCity,
  recentLocations,
  rateDriver,
  driverProfile,
  notificationListing,
  generalSettings,
  cmsContent,
  notificationInfo,
  orderComplete,
  trackOrder,
  cards,
  chargeCard
});

/*  
route: navigator,
import navigator from "./navigator";
*/
