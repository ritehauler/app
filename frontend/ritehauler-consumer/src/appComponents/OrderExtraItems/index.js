// @flow
import React from "react";
import { FlatList } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text } from "../../components";
import { OrderItem } from "../";
import { Strings } from "../../theme";

export default class OrderExtraItems extends React.PureComponent {
  static propTypes = {
    data: PropTypes.array.isRequired
  };

  static defaultProps = {};

  keyExtractor = (item, index) => `${index}${item.entity_id}`;

  /*
  renderItem({ item, index }) {
    console.log("extar item", item, index);
    return <Text>Extra item</Text>;
  }
  */

  renderItem({ item, index }) {
    return <OrderItem data={item} index={index} disableRipple />;
  }

  renderHeader() {
    return (
      <Text size="xxSmall" style={styles.extraItem}>
        {Strings.extraItemsAddedByDriver}
      </Text>
    );
  }

  render() {
    const { data } = this.props;
    return (
      <FlatList
        data={data}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        ListHeaderComponent={this.renderHeader}
      />
    );
  }
}
