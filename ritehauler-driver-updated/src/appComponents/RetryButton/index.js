// @flow
import React, { PureComponent } from "react";
import LinearGradient from "react-native-linear-gradient";
import PropTypes from "prop-types";
import { Text, TouchableWithoutFeedback, View } from "react-native";

import {
  Colors,
  Strings,
  ApplicationStyles,
  Metrics,
  Fonts
} from "../../theme";
import styles from "./styles";
import { GRADIENT_START, GRADIENT_END } from "../../constant";
import Utils from "../../util";

export default class RetryButton extends PureComponent {
  state = {
    text: this.props.text
  };

  _renderText() {
    const { text } = this.state;
    return (
      <TouchableWithoutFeedback onPress={() => this.props.onPress()}>
        <View
          style={{
            flex: 1,
            backgroundColor: Colors.background.login,
            alignItems: "center",
            justifyContent: "center"
          }}
        >
          <Text
            style={[
              ApplicationStyles.dBold16,
              {
                color: Colors.text.orange,
                fontSize: Fonts.size.medium,
                paddingHorizontal: Metrics.doubleBaseMargin * 2
              }
            ]}
            ref={ref => {
              this.textView = ref;
            }}
          >
            {text}
          </Text>
        </View>
      </TouchableWithoutFeedback>
    );
  }

  render() {
    return (
      <LinearGradient
        colors={Colors.lgColArray}
        style={styles.linearGradient}
        start={GRADIENT_START}
        end={GRADIENT_END}
      >
        {this._renderText()}
      </LinearGradient>
    );
  }
}
