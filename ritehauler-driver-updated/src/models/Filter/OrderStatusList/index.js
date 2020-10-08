// @flow
import React from "react";
import { FlatList } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import Item from "../Item";

export default class OrderStatusList extends React.PureComponent {
  static propTypes = {
    data: PropTypes.array.isRequired,
    onPressItem: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this._renderItem = this._renderItem.bind(this);
  }

  _keyExtractor(item) {
    return `${item.id}`;
  }

  _renderItem({ item }) {
    return <Item data={item} onPress={this.props.onPressItem} />;
  }

  _renderListSeparator() {
    return null;
  }

  render() {
    return (
      <FlatList
        data={this.props.data}
        renderItem={this._renderItem}
        keyExtractor={this._keyExtractor}
        ItemSeparatorComponent={this._renderListSeparator}
        style={styles.list}
      />
    );
  }
}
