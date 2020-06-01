// @flow
import _ from "lodash";
import React, { Component } from "react";
import { Keyboard, FlatList } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { userDataHelper } from "../../dataHelper";
import { Separator } from "../../components";
import styles from "./styles";
import { Strings } from "../../theme";
import Util from "../../util";

import Item from "./Item";

import { ENTITY_TYPE_ID_CUSTOMER } from "../../config/WebService";
import { request as updateToggleRequest } from "../../actions/UpdateNotificationToggleActions";

class Settings extends Component {
  static propTypes = {
    updateToggleRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.renderItem = this.renderItem.bind(this);
    this.keyExtractor = this.keyExtractor.bind(this);
    this.onToggle = this.onToggle.bind(this);
  }

  state = {
    data: [
      {
        id: 1,
        title: Strings.appNotifications,
        key: "system_notify",
        isOn: userDataHelper.isAppNotificationEnable(this.props.user)
      },
      {
        id: 2,
        title: Strings.orderNotifications,
        key: "is_notify",
        isOn: userDataHelper.isOrderNotificationEnable(this.props.user)
      }
    ]
  };

  componentWillMount() {
    Keyboard.dismiss();
  }

  onToggle(index, toggleValue) {
    const { networkInfo } = this.props;
    const { data } = this.state;
    const isInternetConnected = networkInfo.isNetworkConnected;
    if (isInternetConnected) {
      const newData = _.cloneDeep(data);
      newData[index].isOn = toggleValue;
      this.setState({ data: newData });
      this.sendUpdateNotificationRequest(newData);
    } else {
      Util.alert(Strings.noInternetMessage);
    }
  }

  sendUpdateNotificationRequest(data) {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      entity_id: this.props.user.entity_id,
      mobile_json: 1
    };
    for (let i = 0; i < data.length; i += 1) {
      const item = data[i];
      payload[item.key] = item.isOn ? "1" : "0";
    }
    this.props.updateToggleRequest(payload);
  }

  keyExtractor(item) {
    return `${item.id}`;
  }

  renderItem({ item, index }) {
    return <Item data={item} onToggle={this.onToggle} index={index} />;
  }

  renderListSeparator() {
    return <Separator />;
  }

  render() {
    const { data } = this.state;
    return (
      <FlatList
        data={data}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        ItemSeparatorComponent={this.renderListSeparator}
        style={styles.container}
        contentContainerStyle={styles.contentContainerStyle}
      />
    );
  }
}

const mapStateToProps = store => ({
  user: store.user.data,
  networkInfo: store.networkInfo
});
const actions = { updateToggleRequest };

export default connect(
  mapStateToProps,
  actions
)(Settings);
