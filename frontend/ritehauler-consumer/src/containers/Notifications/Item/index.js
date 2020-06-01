// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../../components";
import Utils from "../../../util";
import { DATE_TIME_FORMAT, DISPLAY_DATE_TIME_FORMAT } from "../../../constants";

export default class Item extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  onPressItem() {
    Utils.handleNotificationNavigation(this.props.data);
  }

  _renderDot() {
    const { is_read } = this.props.data;
    if (is_read === 0) {
      return <Image source={Images.unread} style={styles.dot} />;
    }
    return null;
  }

  _renderContent() {
    const { notification_message, created_at } = this.props.data;
    const displayDate = Utils.formatDateLocal(
      created_at,
      DATE_TIME_FORMAT,
      DISPLAY_DATE_TIME_FORMAT
    );
    const fromNow = Utils.dateFromNowLocal(created_at, DATE_TIME_FORMAT);
    return (
      <View style={styles.content}>
        <Text style={styles.text} size="small" type="medium">
          {notification_message || "No Message found"}
        </Text>
        <View style={styles.dateContainer}>
          <Text size="xxSmall" color="secondary" style={styles.date}>
            {displayDate}
          </Text>
          <Text size="xxSmall" color="secondary">
            {fromNow}
          </Text>
        </View>
      </View>
    );
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this.onPressItem}>
        {this._renderDot()}
        {this._renderContent()}
      </ButtonView>
    );
  }
}