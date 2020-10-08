// @flow
import _ from "lodash";
import { connect } from "react-redux";
import React, { Component } from "react";
import { Actions } from "react-native-router-flux";
import { Platform, Alert } from "react-native";
import { Call } from "react-native-openanything";
import PropTypes from "prop-types";
import ListItem from "./ListItem";
import Header from "./Header";
import { FlatList } from "../";
import { Colors, Images, Metrics, Fonts } from "../../theme";
import { logout } from "../../actions/UserActions";
import { request } from "../../actions/DutyToggleActions";
import Util from "../../util";
import { MENU_TYPE, USER_ENTITY_TYPE_ID } from "../../constant";
import {
  CMS_PRIVACY_POLICY,
  CMS_SLUG_ABOUT,
  CMS_TERMS_OF_SERVICES
} from "../../config/WebService";

// redux imports
import { selectCachedLoginUser } from "../../reducers/reduxSelectors";

class ScreenList extends Component {
  static propTypes = {
    data: PropTypes.array,
    header: PropTypes.object
  };

  static defaultProps = {
    header: {
      title: "",
      rightImage: Images.iconCategoryNavigation,
      onPress: () => {
        Actions.drawerClose();
        setTimeout(
          () => Actions.profile(),
          Util.isPlatformAndroid() ? 50 : 300
        );
      }
    },
    data: []
  };

  static getDerivedStateFromProps(nextProps, prevState) {
    if (
      nextProps.customerServiceNumber !==
        prevState.drawerData[prevState.drawerData.length - 2].rightText ||
      nextProps.pendingOrders !== prevState.drawerData[1].rightText ||
      prevState.drawerData[2].toggle.value !== nextProps.user.on_duty
    ) {
      let newState = prevState.drawerData;

      /* set new customer service number */
      newState[prevState.drawerData.length - 2].rightText =
        nextProps.customerServiceNumber;

      /* set new pending orders */
      newState[1].rightText = nextProps.pendingOrders;

      /* set new toggle value */
      newState[2].toggle.value = nextProps.user.on_duty ? true : false;

      return {
        drawerData: newState
      };
    }

    // Return null to indicate no change to state.
    return null;
  }

  state = {
    drawerData: [
      {
        title: "Statistics",
        color: Colors.text.navBarText,
        rightImage: Images.next,
        onPress: () => {
          this.handleDrawerPress("statistics");
        },
        type: "regular"
      },
      {
        title: "My Orders",
        color: Colors.text.navBarText,
        onPress: () => {
          this.handleDrawerPress("myOrders");
        },
        type: "regular",
        rightImage: Images.next,
        rightText: this.props.pendingOrders,
        rightTextcolor: Colors.accent2,
        rightTextSize: Fonts.size.small,
        rightTextType: "regular"
      },
      {
        title: "Duty",
        color: Colors.text.navBarText,
        type: "regular",
        toggle: {
          value: this.props.user.on_duty ? true : false,
          serviceEndPoint: ""
        }
      },
      {
        title: "Notifications",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () => Actions.Notifications(),
        rightImage: Images.next
      },
      {
        title: "About",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () =>
          Actions.content({ title: "About", slug: CMS_SLUG_ABOUT }),
        rightImage: Images.next
      },
      {
        title: "Terms Of Services",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () =>
          Actions.content({
            title: "Terms Of Services",
            slug: CMS_TERMS_OF_SERVICES
          }),
        rightImage: Images.next
      },
      {
        title: "Privacy Policy",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () =>
          Actions.content({
            title: "Privacy Policy",
            slug: CMS_PRIVACY_POLICY
          }),
        rightImage: Images.next
      },
      {
        title: "Customer Service",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () => {
          this.callToCustomerService(this.props.customerServiceNumber);
        },
        rightText: this.props.customerServiceNumber,
        rightTextcolor: Colors.text.secondary
      },
      {
        title: "Logout",
        color: Colors.text.navBarText,
        type: "regular",
        onPress: () => {
          Alert.alert(
            "Logout",
            "Are you sure to logout?",
            [
              {
                text: "Cancel",
                onPress: () => {},
                style: "cancel"
              },
              { text: "OK", onPress: () => this.logout() }
            ],
            { cancelable: false }
          );
        }
      }
    ],
    dutyApiHit: false
  };

  callToCustomerService(customerSupportPhone) {

    //alert(customerSupportPhone)
    if (customerSupportPhone !== "+0 000 (000) (0000)") {
      // alert('hi')
      Call(customerSupportPhone).catch(err => Util.showMessage(err, "error"));
    }
  }

  logout() {
    this.props.logout({
      entity_type_id: USER_ENTITY_TYPE_ID,
      entity_id: this.props.user.entity_id
    });
  }

  handleDrawerPress(route) {
    Actions.drawerClose();
    setTimeout(() => Actions.push(route), Util.isPlatformAndroid() ? 50 : 250);
  }

  _renderHeader = header => {
    return (
      <Header
        {...header}
        title={`${this.props.user.first_name} ${this.props.user.last_name}`}
        type={Fonts.type.dBold}
        size={Fonts.size.large}
        color={Colors.text.primary}
      />
    );
  };

  _renderItem = info => (
    <ListItem
      {...info.item}
      onToggle={value => this.handleTogglePress(value)}
      index={info.index}
    />
  );

  handleTogglePress = toggleValue => {
    this.props.request({
      entity_type_id: USER_ENTITY_TYPE_ID,
      entity_id: this.props.user.entity_id,
      on_duty: toggleValue ? 1 : 0,
      mobile_json: 1
    });
  };

  _renderListSeparator = () => null;

  render() {
    const { data, header } = this.props;
    const statusStyle =
      Platform.OS === "android" && Platform.Version >= 19
        ? { marginTop: Metrics.statusBarHeight }
        : { marginTop: Metrics.statusBarHeight };
    return (
      <FlatList
        ListHeaderComponent={this._renderHeader(header)}
        style={[{ backgroundColor: Colors.background.login }, statusStyle]}
        data={this.state.drawerData}
        renderItem={this._renderItem}
        keyExtractor={({ title }) => title}
        ItemSeparatorComponent={this._renderListSeparator}
      />
    );
  }
}

const mapStateToProps = ({ user, generalSettings }) => ({
  user: selectCachedLoginUser(user.data),
  customerServiceNumber: generalSettings.data.customer_support_phone
    ? generalSettings.data.customer_support_phone
    : "+0 000 (000) (0000)",
  pendingOrders: generalSettings.data.pending_order
    ? `${generalSettings.data.pending_order} Pending`
    : ""
});

const actions = { logout, request };

export default connect(mapStateToProps, actions)(ScreenList);
