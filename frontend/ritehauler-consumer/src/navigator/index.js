// @flow
import React from "react";
import { connect } from "react-redux";
import { Alert } from "react-native";
import {
  Stack,
  Scene,
  Router,
  Actions,
  ActionConst,
  Drawer
} from "react-native-router-flux";

import styles from "./styles";
import {
  Login,
  SignUp,
  ForgotPassword,
  VerifyPhone,
  ConsumerLocation,
  SearchLocation,
  AddItem,
  AddItemDetails,
  SearchItemName,
  CreateOrder,
  SelectVehicle,
  DeliveryProfessionals,
  OrderSummary,
  PaymentMethods,
  AddCard,
  Orders,
  OrderDetail,
  Notifications,
  Profile,
  ChangePassword,
  DriverProfile,
  RateDriver,
  Content,
  TrackOrder,
  UpdatePhoneNumber,
  Settings
} from "../containers";
import {
  LeftViewNavigation,
  NavigationDrawer,
  RightViewNavigation,
  CustomNavBar
} from "../appComponents";
import { Images, Metrics, Strings } from "../theme";

/*
import Utils from "../util";
import { BACK_SCENES } from "../constants";
function onBackPress() {
  const currentScene = Actions.currentScene;

  if (currentScene === "createOrder") {
    Utils.showConfirmationDialog();
    return true;
  } else if (BACK_SCENES.includes(currentScene)) {
    return false;
  }

  Actions.pop();
  return true;
}
*/

const navigator = Actions.create(
  <Stack
    key="root"
    titleStyle={styles.title}
    initial
    headerStyle={styles.headerLogin}
  >
    <Scene
      hideNavBar
      initial
      key="login"
      component={Login}
      type={ActionConst.RESET}
    />
    <Scene
      key="signUp"
      component={SignUp}
      title={Strings.signUp}
      renderLeftButton={() => <LeftViewNavigation />}
      drawerLockMode="locked-closed"
    />

    <Scene
      key="forgotPassword"
      component={ForgotPassword}
      title={Strings.forgotPassword}
      renderLeftButton={() => <LeftViewNavigation />}
    />
    <Scene
      key="verifyPhone"
      component={VerifyPhone}
      title={Strings.verification}
      renderLeftButton={() => <LeftViewNavigation />}
    />
    <Scene
      key="updatePhoneNumber"
      component={UpdatePhoneNumber}
      title={Strings.addPhoneNumber}
      renderLeftButton={() => <LeftViewNavigation />}
    />

    <Scene
      component={Content}
      title=""
      key="content"
      drawerLockMode="locked-closed"
      renderLeftButton={() => <LeftViewNavigation />}
    />

    <Drawer
      drawer
      key="home"
      hideNavBar
      type={ActionConst.RESET}
      contentComponent={NavigationDrawer}
      drawerWidth={Metrics.screenWidth * 0.9}
      headerStyle={styles.headerDashBoard}
      drawerImage={Images.drawer}
    >
      <Scene hideNavBar>
        <Stack key="root2">
          <Scene
            component={ConsumerLocation}
            title={Strings.booking}
            key="consumerLocation"
            type={ActionConst.RESET}
            renderLeftButton={() => (
              <LeftViewNavigation
                image={Images.drawer}
                action={Actions.drawerOpen}
              />
            )}
          />
          <Scene
            key="verifyPhone"
            component={VerifyPhone}
            title={Strings.verification}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            key="updatePhoneNumber"
            component={UpdatePhoneNumber}
            title={Strings.addPhoneNumber}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={ChangePassword}
            title={Strings.changePassword}
            key="changePassword"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={Profile}
            navBar={() => <CustomNavBar title={Strings.myProfile} />}
            key="profile"
            drawerLockMode="locked-closed"
          />

          <Scene
            component={DriverProfile}
            navBar={CustomNavBar}
            key="driverProfile"
            drawerLockMode="locked-closed"
          />

          <Scene
            component={Content}
            title=""
            key="content"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={Notifications}
            title={Strings.notifications}
            key="notifications"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            component={Settings}
            title={Strings.settings}
            key="settings"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            component={RateDriver}
            title={Strings.rateDriver}
            key="rateDriver"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            component={TrackOrder}
            title="RH124"
            key="trackOrder"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            component={OrderDetail}
            title=""
            key="orderDetail"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={Orders}
            title={Strings.myOrders}
            key="orders"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
            renderRightButton={() => (
              <RightViewNavigation image={Images.filter} />
            )}
          />
          <Scene
            component={OrderSummary}
            title={Strings.orderSummary}
            key="orderSummary"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={AddCard}
            title={Strings.addCard}
            key="addCard"
            renderRightButton={() => <RightViewNavigation />}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={PaymentMethods}
            title={Strings.paymentMethod}
            key="paymentMethod"
            drawerLockMode="locked-closed"
            renderRightButton={() => (
              <RightViewNavigation text="Add" action={Actions.addCard} />
            )}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={DeliveryProfessionals}
            title={Strings.deliveryProfessionals}
            key="deliveryProfessional"
            drawerLockMode="locked-closed"
            renderRightButton={() => (
              <RightViewNavigation text="Skip" action={Actions.paymentMethod} />
            )}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={SelectVehicle}
            title={Strings.selectVehicle}
            key="selectVehicle"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={CreateOrder}
            title={Strings.createOrder}
            key="createOrder"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={AddItem}
            title={Strings.addItem}
            key="addItem"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={SearchItemName}
            title={Strings.search}
            key="searchItemName"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={SearchLocation}
            title={Strings.search}
            key="searchLocation"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            component={AddItemDetails}
            title={Strings.addItem}
            key="addItemDetail"
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
        </Stack>
      </Scene>
    </Drawer>
  </Stack>
);

export default () => (
  <AppNavigator uriPrefix="ritehauler.com" navigator={navigator} />
);

const AppNavigator = connect()(Router);

/*
 backAndroidHandler={onBackPress}
*/
