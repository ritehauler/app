// @flow
import React, { Component } from "react";
import { Call } from "react-native-openanything";
import { Actions } from "react-native-router-flux";
import { FlatList, View } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { userDataHelper } from "../../dataHelper";
import { Images, Strings } from "../../theme";
import { Message } from "../../models";
import { Loader } from "../../components";
import ListItem from "./ListItem";
import Header from "./Header";
import styles from "./styles";
import Util from "../../util";

import { request as logoutRequest } from "../../actions/LogoutActions";
import { CMS_SLUG_ABOUT } from "../../config/WebService";

class NavigationDrawer extends Component {
  static propTypes = {
    logoutIsFetching: PropTypes.bool.isRequired,
    user: PropTypes.object.isRequired,
    logoutRequest: PropTypes.func.isRequired,
    settings: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.showLogoutDialog = this.showLogoutDialog.bind(this);
    this._renderHeader = this._renderHeader.bind(this);
    this.logout = this.logout.bind(this);
    this.getData = this.getData.bind(this);
    this.onPressItem = this.onPressItem.bind(this);
  }

  componentDidMount() {
    // set settings for data handler for later use
    Util.setSettings(this.props.settings);
  }

  onPressItem = (index, customerSupportPhone = "") => () => {
    switch (index) {
      case 0:
        Actions.notifications();
        break;
      case 1:
        Actions.paymentMethod({ showButton: false });
        break;
      case 2:
        Actions.orders();
        break;
      case 3:
        Actions.content({ title: Strings.about, slug: CMS_SLUG_ABOUT });
        break;
      case 4:
        Util.goToTermsOfServices();
        break;
      case 5:
        Util.goToPrivacyPolicy();
        break;
      case 6:
        Actions.settings();
        break;
      case 7:
        this.callToCustomerService(customerSupportPhone);
        break;
      case 8:
        this.showLogoutDialog();
        break;
      default:
    }
  };

  getData() {
    const { settings } = this.props;
    const unreadNotifications = settings.unread_notification
      ? `${settings.unread_notification}`
      : "";
    const pendingOrders = settings.pending_orders
      ? `${settings.pending_orders} ${Strings.statusPendingDrawer}`
      : "";
    const customerSupportPhone =
      settings.general_setting &&
      settings.general_setting.customer_support_phone
        ? settings.general_setting.customer_support_phone
        : "";

    return [
      {
        title: Strings.notifications,
        rightImage: Images.navigation,
        isNotification: true,
        rightTextSize: "small",
        customStyle: styles.notification,
        onPress: this.onPressItem(0),
        rightText: unreadNotifications
      },
      {
        title: Strings.paymentMethod,
        rightImage: Images.navigation,
        onPress: this.onPressItem(1)
      },
      {
        title: Strings.myOrders,
        onPress: this.onPressItem(2),
        rightImage: Images.navigation,
        rightTextSize: "xxSmall",
        isOrder: true,
        rightText: pendingOrders
      },
      {
        title: Strings.about,
        isWallet: true,
        onPress: this.onPressItem(3),
        rightImage: Images.navigation
      },
      {
        title: Strings.termsOfServices,
        onPress: this.onPressItem(4),
        rightImage: Images.navigation
      },
      {
        title: Strings.privacyPolicy,
        onPress: this.onPressItem(5),
        rightImage: Images.navigation
      },
      {
        title: Strings.settings,
        onPress: this.onPressItem(6),
        rightImage: Images.navigation
      },
      {
        title: Strings.customerService,
        onPress: this.onPressItem(7, customerSupportPhone),
        rightText: customerSupportPhone,
        rightTextColor: "secondary",
        rightTextSize: "xxSmall",
        rightTextType: "light"
      },
      {
        title: Strings.logout,
        onPress: this.onPressItem(8)
      }
    ];
  }

  callToCustomerService(customerSupportPhone) {
    if (customerSupportPhone !== "") {
      Call(customerSupportPhone).catch(err => Util.showMessage(err, "error"));
    }
  }

  showLogoutDialog() {
    if (this.logoutModel) {
      Actions.drawerClose();
      setTimeout(() => {
        this.logoutModel.show();
      }, 500);
    }
  }

  logout() {
    this.logoutModel.hide();
    setTimeout(() => {
      Util.logoutUser();
      /*
      const payload = {
        entity_id: this.props.user.entity_id,
        mobile_json: 1
      };
      this.props.logoutRequest(payload);
      */
    }, 500);
  }

  _renderHeader() {
    return <Header title={userDataHelper.getUserName(this.props.user)} />;
  }

  _renderItem(info) {
    return <ListItem {...info.item} />;
  }

  _keyExtractor(item) {
    return item.title;
  }

  _renderListSeparator() {
    return null;
  }

  _renderLogoutModal() {
    return (
      <Message
        ref={ref => {
          this.logoutModel = ref;
        }}
        description={Strings.logoutMessage}
        rightButtonTitle={Strings.yes}
        onPress={this.logout}
        leftButtonTitle={Strings.noThanks}
        isCancelable
      />
    );
  }

  _renderList() {
    return (
      <FlatList
        ListHeaderComponent={this._renderHeader}
        style={styles.list}
        data={this.getData()}
        renderItem={this._renderItem}
        ItemSeparatorComponent={this._renderListSeparator}
        keyExtractor={this._keyExtractor}
      />
    );
  }

  _renderLoading() {
    const { logoutIsFetching } = this.props;
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
        loading={logoutIsFetching}
      />
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderLogoutModal()}
        {this._renderList()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  logoutIsFetching: store.logout.isFetching,
  user: store.user.data,
  settings: store.generalSettings.data
});
const actions = {
  logoutRequest
};

export default connect(
  mapStateToProps,
  actions
)(NavigationDrawer);
