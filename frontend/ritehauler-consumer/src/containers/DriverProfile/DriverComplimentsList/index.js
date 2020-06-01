// @flow
import React from "react";
import { View, FlatList } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text, Separator } from "../../../components";
import { Strings } from "../../../theme";

import ItemReview from "../ItemReview";

export default class DriverComplimentsList extends React.PureComponent {
  static propTypes = {
    data: PropTypes.array.isRequired
  };

  static defaultProps = {};

  _renderSeparator() {
    return <Separator />;
  }

  _renderTitle() {
    return (
      <Text style={styles.title} type="bold">
        {Strings.driverFeedback}
      </Text>
    );
  }

  _renderList() {
    return (
      <FlatList
        data={this.props.data}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        style={styles.list}
        ItemSeparatorComponent={this.renderListSeparator}
      />
    );
  }

  keyExtractor(item) {
    return `${item.package_rate_id}`;
  }

  renderItem({ item }) {
    return <ItemReview data={item} />;
  }

  renderListSeparator() {
    return <View style={styles.separatorList} />;
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderTitle()}
        {this._renderSeparator()}
        {this._renderList()}
      </View>
    );
  }
}
