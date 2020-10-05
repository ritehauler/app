// @flow
import React, { Component } from "react";
import { Provider } from "react-redux";
import { AppRegistry, StatusBar, View, NativeModules } from "react-native";

import configureStore from "./store";
import AppNavigator from "./navigator";
import applyConfigSettings from "./config";
import Utils from "./util";
import {
  registerFCMListener,
  requestPermission
} from "./services/NotificationListener";
import { MessageBar } from "./components";
const reducers = require("./reducers").default;

// redux imports
import NetworkInfo from "./util/NetworkInfo";
import networkInfoListener from "./actions/NetworkInfoActions";
import { request as requestAssignedOrders } from "./actions/AssignedOrders";

applyConfigSettings();

class App extends Component {
  state = {
    isLoading: true,
    store: configureStore(reducers, () => {
      this.setState({ isLoading: false });
    })
  };

  componentDidMount() {
    if (Utils.isPlatformAndroid()) {
      NativeModules.SplashScreen.hide();
    }
    // network info change
    NetworkInfo.networkInfoListener(
      this.state.store.dispatch,
      networkInfoListener
    );

    // notifications ask for permission and register listeners
    requestPermission();
    registerFCMListener(this.state.store.dispatch, requestAssignedOrders);
  }

  componentWillUnmount() {
    // network info change
    NetworkInfo.removeNetworkInfoListener(
      this.state.store.dispatch,
      networkInfoListener
    );
  }

  render() {
    if (this.state.isLoading) {
      return null;
    }
    return (
      <View style={{ flex: 1 }}>
        <StatusBar
          barStyle={
            Utils.isPlatformAndroid() ? "light-content" : "dark-content"
          }
        />
        <Provider store={this.state.store}>
          <AppNavigator />
        </Provider>
        <MessageBar />
      </View>
    );
  }
}

AppRegistry.registerComponent("RiteHaulerDriver", () => App);
