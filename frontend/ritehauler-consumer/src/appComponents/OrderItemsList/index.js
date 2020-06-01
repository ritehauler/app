// @flow
import _ from "lodash";
import React, { Component } from "react";
import { FlatList, ViewPropTypes } from "react-native";
import PropTypes from "prop-types";

import { OrderItem } from "../";
import styles from "./styles";
import { Metrics } from "../../theme";

export default class OrderItemsList extends Component {
  static propTypes = {
    onPressItem: PropTypes.func,
    onPressDelete: PropTypes.func,
    disableRipple: PropTypes.bool,
    customContainerStyle: ViewPropTypes.style,
    showSwipeOut: PropTypes.bool
  };

  static defaultProps = {
    onPressItem: undefined,
    onPressDelete: undefined,
    disableRipple: false,
    customContainerStyle: {},
    showSwipeOut: false
  };

  constructor(props) {
    super(props);
    this.renderItem = this.renderItem.bind(this);
  }
  shouldComponentUpdate(nextProps: Object) {
    return !_.isEqual(nextProps, this.props);
  }

  getReference = () => this.list;

  keyExtractor = (item, index) => `${index}${item.entity_id}`;

  scrollToBottom = () => {
    if (this.listHeight > 0) {
      this.list.scrollToOffset({
        offset: this.listHeight,
        animated: true
      });
    }
  };

  listHeight = 0;

  renderItem({ item, index }) {
    return (
      <OrderItem
        data={item}
        index={index}
        onPressItem={this.props.onPressItem}
        onPressDelete={this.props.onPressDelete}
        disableRipple={this.props.disableRipple}
        showSwipeOut={this.props.showSwipeOut}
      />
    );
  }

  render() {
    const { customContainerStyle, ...rest } = this.props;
    return (
      <FlatList
        renderItem={this.renderItem}
        style={styles.container}
        contentContainerStyle={[
          styles.contentContainerStyle,
          customContainerStyle
        ]}
        onContentSizeChange={(width, height) => {
          this.listHeight =
            height - (Metrics.screenHeight - Metrics.navBarHeight) + 40;
        }}
        {...rest}
        ref={ref => {
          this.list = ref;
        }}
        keyExtractor={this.keyExtractor}
      />
    );
  }
}
