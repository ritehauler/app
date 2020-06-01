// @flow
import React, { Component } from "react";
import { Provider } from "react-redux";
import { View, AppState, StatusBar } from "react-native";
import SplashScreen from "react-native-splash-screen";
//import { Client } from "bugsnag-react-native";

import {
  registerFCMListener,
  requestPermission
} from "./util/NotificationListener";
import networkInfoListener from "./actions/NetworkInfoActions";
import configureStore from "./store";
import AppNavigator from "./navigator";
import { MessageBar } from "./components";
import applyConfigSettings from "./config";
import { AppStyles } from "./theme";
import Utils from "./util";
import NetworkInfo from "./util/NetworkInfo";
import DataHandler from "./util/DataHandler";

const reducers = require("./reducers").default;

//const bugsnag = new Client();

applyConfigSettings();

export default class App extends Component {
  state = {
    isLoading: true,
    store: configureStore(reducers, () => {
      SplashScreen.hide();
      this.setState({ isLoading: false });
      this._storeLoadingCompleted();
    })
  };

  componentDidMount() {
    // app state change
    AppState.addEventListener("change", this._handleAppStateChange);

    // network info change
    NetworkInfo.networkInfoListener(
      this.state.store.dispatch,
      networkInfoListener
    );

    // notifications ask for permission and register listeners
    requestPermission();
  }

  componentWillUnmount() {
    // app state change
    AppState.removeEventListener("change", this._handleAppStateChange);

    // network info change
    NetworkInfo.removeNetworkInfoListener(
      this.state.store.dispatch,
      networkInfoListener
    );
  }

  _storeLoadingCompleted() {
    DataHandler.setStore(this.state.store);
    registerFCMListener(this.state.store);
  }

  _handleAppStateChange = nextAppState => {
    if (nextAppState === "active" && Utils.isPlatformAndroid()) {
      StatusBar.setTranslucent(true);
    }
  };

  render() {
    if (this.state.isLoading) {
      return null;
    }

    return (
      <View style={AppStyles.flex}>
        <Provider store={this.state.store}>
          <AppNavigator />
        </Provider>
        <MessageBar />
      </View>
    );
  }
}
