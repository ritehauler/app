// @flow
import React from "react";
import { connect } from "react-redux";
import {
  Stack,
  Scene,
  Router,
  Actions,
  Drawer,
  ActionConst,
  Modal
} from "react-native-router-flux";

import styles from "./styles";
import { Colors, Images, Metrics } from "../theme";
import { LeftViewNavigation } from "../appComponents";
import { NavigationDrawer, TabButtonLeft, CustomNavBar } from "../components";
import {
  Login,
  ForgotPassword,
  Home,
  Profile,
  OrderSummary,
  OrderReceipt,
  Statistics,
  MyOrders,
  OrderFilter,
  VerifyItems,
  AddItem,
  AddItemDetails,
  SearchItemName,
  RateAgent,
  ChangePassword,
  Content,
  CreateOrder,
  OrderAssignment,
  Notifications,
  Signup,
  Thankyou
} from "../containers";
import {
  forgot,
  login,
  home,
  changePassword,
  orderSummary,
  orderReceipt,
  addItems,
  createOrder,
  verifyItems,
  orderFilter,
  statistics,
  myOrders,
  searchItemName,
  addItemDetail,
  rateAgent,
  content,
  profile,
  orderAssignment,
  notifications,
  signup,
  thankyou
} from "./Keys";

const navigator = Actions.create(
  <Stack
    key="root"
    titleStyle={styles.title}
    headerStyle={styles.loginHeader}
    headerTintColor={Colors.navbar.text}
  >
    <Scene title=""
     key={login} 
     component={Login}
     renderLeftButton={() => <LeftViewNavigation />}
 />
    <Scene 
          title="Sign Up"
          component={Signup}
          key = {signup}
          renderLeftButton={() => <LeftViewNavigation />}
          />
      <Scene
        title=""
        component={Thankyou}
        key={thankyou}
        renderLeftButton={() => <LeftViewNavigation/>}
      />
    <Scene
      title="Forgot Password"
      key={forgot}
      headerStyle={styles.header}
      component={ForgotPassword}
      renderLeftButton={() => <LeftViewNavigation />}
    />

    <Drawer
      hideNavBar
      drawer
      key={home}
      drawerWidth={Metrics.screenWidth * 0.85}
      titleStyle={styles.loginTitle}
      headerStyle={[styles.header]}
      type={ActionConst.RESET}
      drawerImage={Images.iconMenu}
      contentComponent={NavigationDrawer}
    >
      <Scene hideNavBar>
        <Stack
          key="root2"
          headerStyle={styles.header}
          titleStyle={styles.title}
        >
          <Scene
            title="Rite Hauler"
            key="_home"
            component={Home}
            renderLeftButton={() => (
              <TabButtonLeft
                imagesArray={["iconMenu"]}
                actions={[() => Actions.drawerOpen()]}
              />
            )}
          />
          

          <Scene
            title="Order Summary"
            key={orderSummary}
            component={OrderSummary}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="Order Receipt"
            key={orderReceipt}
            component={OrderReceipt}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="Statistics"
            key={statistics}
            component={Statistics}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="My Orders"
            key={myOrders}
            component={MyOrders}
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            title="Filter"
            key={orderFilter}
            component={OrderFilter}
            renderLeftButton={() => (
              <TabButtonLeft
                imagesArray={["filter"]}
                actions={[() => Actions.pop()]}
              />
            )}
          />

          <Scene
            title="Verify Items"
            key={verifyItems}
            component={VerifyItems}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            //initial
            title="Order Assignment"
            key={orderAssignment}
            component={OrderAssignment}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            title="Add Item"
            key={addItems}
            component={AddItem}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="Create Order"
            key={createOrder}
            component={CreateOrder}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="Search"
            key={searchItemName}
            component={SearchItemName}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title="Add Item"
            key={addItemDetail}
            component={AddItemDetails}
            drawerLockMode="locked-closed"
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            //initial
            title="Rate The Customer"
            key={rateAgent}
            component={RateAgent}
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            title="Content"
            key={content}
            component={Content}
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            title="Notifications"
            key={notifications}
            component={Notifications}
            renderLeftButton={() => <LeftViewNavigation />}
          />

          <Scene
            title="Change Password"
            key={changePassword}
            component={ChangePassword}
            renderLeftButton={() => <LeftViewNavigation />}
          />
          <Scene
            title=""
            key={profile}
            component={Profile}
            navBar={CustomNavBar}
          />
        </Stack>
      </Scene>
    </Drawer>
  </Stack>
);

export default () => <AppNavigator navigator={navigator} />;

const AppNavigator = connect()(Router);
