import React from "react";
import PropTypes from "prop-types";
import { BackHandler as BackHandlerRN } from "react-native";

export default class BackHandler extends React.PureComponent {
  static propTypes = {
    onBackPress: PropTypes.func
  };

  static defaultProps = {
    onBackPress: () => {}
  };

  componentDidMount() {
    BackHandlerRN.addEventListener("hardwareBackPress", this.handleBack);
  }

  componentWillUnmount() {
    BackHandlerRN.addEventListener("hardwareBackPress", this.handleBack);
  }

  handleBack = () => this.props.onBackPress();

  render() {
    return null;
  }
}
