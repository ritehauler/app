// @flow
import React from "react";
import { View, FlatList } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text, Separator } from "../../../components";
import { Strings } from "../../../theme";

import ItemStatistics from "../ItemStatistics";

export default class DriverStatistics extends React.PureComponent {
  static propTypes = {
    data: PropTypes.array.isRequired
  };

  static defaultProps = {};

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  _renderTitle() {
    return (
      <Text style={styles.title} type="bold">
        {Strings.driverCompliments}
      </Text>
    );
  }

  _renderList() {
    return (
      <FlatList
        data={this.props.data}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        ItemSeparatorComponent={this.renderListSeparator}
        showsHorizontalScrollIndicator={false}
        style={styles.list}
        horizontal
      />
    );
  }

  keyExtractor(item) {
    return `${item.attribute_option_id}`;
  }

  renderItem({ item }) {
    return <ItemStatistics data={item} />;
  }

  renderListSeparator() {
    return null;
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

/*
{this._renderComplimentStatistics()}
*/
/*
  _renderItem(text, count, image) {
    return (
      <View>
        <Image source={Images[image]} style={styles.image} />
        <Text
          textAlign="center"
          size="xxSmall"
          type="light"
          style={styles.text}
        >
          {text}
        </Text>
        <View style={styles.badge}>
          <Text color="tertiary" size="xxxSmall" type="light">
            {count}
          </Text>
        </View>
      </View>
    );
  }

  _renderComplimentStatistics() {
    const { fiveStar, greatAttitude, expertNavigation } = this.props.data;
    return (
      <View style={styles.contentContainer}>
        {this._renderItem(Strings.fiveStarService, fiveStar, "fiveStar")}
        {this._renderItem(
          Strings.greatAttitude,
          greatAttitude,
          "greatAttitude"
        )}
        {this._renderItem(
          Strings.expertNavigation,
          expertNavigation,
          "expertNavigation"
        )}
      </View>
    );
  }
  */
