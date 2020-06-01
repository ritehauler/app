// @flow
import React from "react";
import { FlatList } from "react-native";
import PropTypes from "prop-types";

import Item from "../Item";

export default class Feedback extends React.PureComponent {
  static propTypes = {
    data: PropTypes.array.isRequired
  };

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
    this._renderItem = this._renderItem.bind(this);
  }

  onPressItem(isSelected, keyword) {
    if (isSelected && this.selectedReviews.indexOf(keyword) === -1) {
      this.selectedReviews.push(keyword);
    } else {
      this.selectedReviews = this.selectedReviews.filter(v => v !== keyword);
    }
  }

  getReviewsList = () => {
    return this.selectedReviews;
  };

  _keyExtractor(item) {
    return `${item.value}`;
  }

  _renderItem({ item }) {
    return <Item data={item} onPress={this.onPressItem} />;
  }

  _renderListSeparator() {
    return null;
  }

  selectedReviews = [];

  render() {
    return (
      <FlatList
        data={this.props.data}
        renderItem={this._renderItem}
        keyExtractor={this._keyExtractor}
        ItemSeparatorComponent={this._renderListSeparator}
      />
    );
  }
}
