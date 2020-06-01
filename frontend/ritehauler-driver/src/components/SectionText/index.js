//@flow
import React, { Component } from "react";
import PropTypes from "prop-types";
import { Text } from "../../components";
import styles from "./styles";
import { ApplicationStyles } from "../../theme";

class SectionText extends Component {
  static propTypes = {
    title: PropTypes.string
  };

  static defaultProps = {
    title: ""
  };

  render() {
    const { ...rest } = this.props;

    return (
      <Text style={[styles.titleStyle, ApplicationStyles.re13Black]} {...rest}>
        {this.props.title}
      </Text>
    );
  }
}

export default SectionText;
