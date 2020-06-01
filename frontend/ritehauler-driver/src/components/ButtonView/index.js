// @flow
import React from "react";
import PropTypes from "prop-types";
import {
  TouchableOpacity,
  TouchableWithoutFeedback,
  View,
  TouchableNativeFeedback
} from "react-native";
import Util from "../../util";

let disableClick = false;

export default class ButtonView extends React.PureComponent {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    children: PropTypes.node.isRequired
  };

  static defaultProps = {
    style: {},
    disableClick: false
  };

  _onPress = () => {
    if (!disableClick) {
      disableClick = true;
      if (this.props.onPress) {
        this.props.onPress();
      }

      setTimeout(() => {
        disableClick = false;
      }, 500);
    }
  };

  render() {
    const { style, children, ...rest } = this.props;

    if (Util.isPlatformAndroid()) {
      return (
        <TouchableNativeFeedback {...rest} onPress={this._onPress}>
          <View style={style}>{this.props.children}</View>
        </TouchableNativeFeedback>
      );
    }

    return (
      <TouchableOpacity style={style} {...rest} onPress={this._onPress}>
        {this.props.children}
      </TouchableOpacity>
    );
  }
}
