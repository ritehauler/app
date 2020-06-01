// @flow
import React from "react";
import { View, Image } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";

import { Images, Strings, Colors } from "../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../components";
import { GRADIENT_START, GRADIENT_END } from "../../constants";

export default class CustomNavBar extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string
  };

  static defaultProps = { title: "" };

  constructor(props) {
    super(props);
    this.onPressBack = this.onPressBack.bind(this);
  }

  onPressBack() {
    Actions.pop();
  }

  render() {
    const { title } = this.props;
    return (
      <LinearGradient
        colors={Colors.background.gradient}
        start={GRADIENT_START}
        end={GRADIENT_END}
      >
        <View style={styles.navBar}>
          <ButtonView style={styles.back} onPress={this.onPressBack}>
            <Image source={Images.back_white} />
          </ButtonView>
          {title !== "" && (
            <Text
              style={styles.myProfile}
              type="bold2"
              size="large"
              color="tertiary"
            >
              {Strings.myProfile}
            </Text>
          )}
        </View>
      </LinearGradient>
    );
  }
}
