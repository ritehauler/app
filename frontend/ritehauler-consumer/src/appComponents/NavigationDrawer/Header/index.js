// @flow
import React from "react";
import { Image } from "react-native";
import { Actions } from "react-native-router-flux";

import PropTypes from "prop-types";

import { ButtonView, Text } from "../../../components";
import { Images } from "../../../theme";
import styles from "./styles";

class Header extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired
  };

  static defaultProps = {};

  renderTitle() {
    const { title } = this.props;
    return (
      <Text type="bold2" size="large" numberOfLines={1} style={styles.title}>
        {title}
      </Text>
    );
  }

  renderRightImage() {
    return <Image source={Images.next} />;
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={() => Actions.profile()}>
        {this.renderTitle()}
        {this.renderRightImage()}
      </ButtonView>
    );
  }
}

export default Header;
