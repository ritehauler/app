// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";

export default class Item extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    onPress: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  onPressItem() {
    const { onPress, ...rest } = this.props;
    onPress({ ...rest });
  }

  _renderItem() {
    const { title } = this.props;
    return (
      <View style={styles.itemContainer}>
        <Image source={Images.pin} />
        <Text style={styles.title} size="small" numberOfLines={1}>
          {title}
        </Text>
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this.onPressItem}>
        {this._renderItem()}
        {this._renderSeparator()}
      </ButtonView>
    );
  }
}
