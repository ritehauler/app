// @flow
import ScrollableTabView from "react-native-scrollable-tab-view";
import { connect } from "react-redux";
import React, { Component } from "react";
import { View, BackHandler, Alert } from "react-native";
import moment from "moment";
import { Actions } from "react-native-router-flux";
import styles from "./styles";
import { Colors, Fonts } from "../../theme";
import { Tab } from "../../components";
import OrderMapView from "./OrderMapView";
import Orders from "./Orders";
import Utils from "../../util";
import { DATE_FORMAT } from "../../constant";
import { home } from "../../navigator/Keys";
import WithAppStateAndLocation from "../HOC/WithAppStateAndLocation";
import WithBackgroundTimer from "../HOC/WithBackgroundTimer";

// redux funcs
import { currentLocation } from "../../actions/LocationActions";
import { request as generalSettingsRequest } from "../../actions/GeneralSettingsActions";
import { request as orderDriverTrackingRequest } from "../../actions/OrderDriverTrackingActions";
import {
  selectorTracking,
  selectorLocation,
  selectorBackgroundLocation,
  selectCachedLoginUser
} from "../../reducers/reduxSelectors";

class Home extends Component {
  constructor(props) {
    super(props);
    this.switchTab = this.switchTab.bind(this);

    if (Utils.isPlatformAndroid()) {
      BackHandler.addEventListener("hardwareBackPress", this.handleBackPress);
      this.handleBackPress = this.handleBackPress.bind(this);
    }
  }

  componentDidMount() {
    this.props.generalSettingsRequest({
      driver_id: this.props.user.entity_id
    });
  }

  componentWillUnmount() {
    if (Utils.isPlatformAndroid()) {
      BackHandler.removeEventListener(
        "hardwareBackPress",
        this.handleBackPress
      );
    }
  }

  handleBackPress = () => {
    if (Actions.currentScene === "_home") {
      this.showExitAlert();
      return true;
    }
    return false;
  };

  showExitAlert() {
    Alert.alert(
      "Exit App",
      "Are you sure to exit?",
      [
        {
          text: "Cancel",
          onPress: () => {},
          style: "cancel"
        },
        { text: "OK", onPress: () => BackHandler.exitApp() }
      ],
      { cancelable: false }
    );
  }

  switchTab() {
    this.scrollableTabViewRef.goToPage(0);
  }

  renderTabs() {
    return (
      <ScrollableTabView
        ref={ref => {
          this.scrollableTabViewRef = ref;
        }}
        locked
        prerenderingSiblingsNumber={0}
        initialPage={0}
        renderTabBar={() => (
          <Tab
            badge={3}
            fontType={Fonts.type.dBold}
            activeTextColor={Colors.text.primary}
            inactiveTextColor={Colors.text.quaternary}
          />
        )}
      >
        <OrderMapView tabLabel="Map View" />
        <Orders tabLabel="Orders" switchTab={this.switchTab} />
      </ScrollableTabView>
    );
  }

  render() {
    return <View style={styles.containerStyle}>{this.renderTabs()}</View>;
  }
}

const mapStateToProps = state => {
  return {
    tracking: selectorTracking(state.location),
    location: selectorLocation(state.location),
    backgroundLocation: selectorBackgroundLocation(state.location),
    user: selectCachedLoginUser(state.user.data),
    assignedOrdersLength: state.assignedOrders.data.length,
    orderEntityID: state.todaysOrders.data.length
      ? state.todaysOrders.data[0].entity_id
      : undefined
  };
};

const actions = {
  currentLocation,
  orderDriverTrackingRequest,
  generalSettingsRequest
};

export default connect(mapStateToProps, actions)(
  WithAppStateAndLocation(WithBackgroundTimer(Home))
);
