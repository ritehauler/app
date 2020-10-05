// @flow
import _ from "lodash";
import React, { Component } from "react";
import { FlatList, ViewPropTypes } from "react-native";
import PropTypes from "prop-types";

import OrderItem from "../../containers/OrderSummary/OrderItem";
import styles from "./styles";

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

  keyExtractor(item, index) {
    return `${index}`;
  }

  renderItem({ item, index }) {
    return (
      <OrderItem
        title={item.title}
        dimensions={item.dimensions}
        quantity={item.quantity}
        dollar={item.dollar}
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
    const { customContainerStyle, renderItem, ...rest } = this.props;
    return (
      <FlatList
        renderItem={
          this.props.renderItem ? this.props.renderItem : this.renderItem
        }
        keyExtractor={this.keyExtractor}
        style={styles.container}
        contentContainerStyle={[
          styles.contentContainerStyle,
          customContainerStyle
        ]}
        ref={ref => {
          this.list = ref;
        }}
        {...rest}
      />
    );
  }
}
