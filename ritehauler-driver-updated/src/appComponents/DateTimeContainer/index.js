// @flow
import React from "react";
import { Image, View } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../components";

export default class DateTimeContainer extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    value: PropTypes.string.isRequired,
    placeholder: PropTypes.string.isRequired,
    onPress: PropTypes.func.isRequired,
    customStyles: View.PropTypes
  };

  static defaultProps = {
    customStyles: {}
  };

  state = { value: this.props.value };

  setValue = value => {
    this.setState({ value });
  };

  getValue = () => this.state.value;

  render() {
    const { title, placeholder, onPress, customStyles } = this.props;
    const { value } = this.state;
    return (
      <ButtonView style={[styles.container, customStyles]} onPress={onPress}>
        <Text type="bold" style={styles.title}>
          {title}
        </Text>
        <Text style={styles.value} type="light" size="xSmall">
          {value || placeholder}
        </Text>
        <Image source={Images.navigation} style={styles.navigation} />
      </ButtonView>
    );
  }
}
