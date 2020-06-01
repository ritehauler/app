// @flow
import React from "react";
import { Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../theme";
import styles from "./styles";
import { ButtonView } from "../../components";

export default class Checkbox extends React.PureComponent {
  static propTypes = {
    isChecked: PropTypes.bool
  };

  static defaultProps = {
    isChecked: false
  };

  constructor(props) {
    super(props);
    this.onPressCheckBox = this.onPressCheckBox.bind(this);
  }

  state = {
    isChecked: this.props.isChecked
  };

  onPressCheckBox() {
    this.setState({ isChecked: !this.state.isChecked });
  }

  isChecked = () => this.state.isChecked;

  render() {
    const { isChecked } = this.state;
    const checkBoxImage = isChecked
      ? Images.checkboxSelect
      : Images.checkboxUnselect;
    return (
      <ButtonView
        style={styles.checkbox}
        onPress={this.onPressCheckBox}
        disableRipple
        enableClick
      >
        <Image source={checkBoxImage} />
      </ButtonView>
    );
  }
}
