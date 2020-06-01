// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images, Strings } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";

export default class SectionFooter extends React.PureComponent {
  static propTypes = {
    onPress: PropTypes.func.isRequired
  };

  static defaultProps = {};

  _renderItem() {
    return (
      <View style={styles.viewAllContainer}>
        <Text size="small" style={styles.viewAllText}>
          {Strings.viewAllRecentLocations}
        </Text>
        <Image source={Images.viewAll} />
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  render() {
    const { onPress } = this.props;
    return (
      <ButtonView style={styles.container} onPress={onPress}>
        {this._renderItem()}
        {this._renderSeparator()}
      </ButtonView>
    );
  }
}
