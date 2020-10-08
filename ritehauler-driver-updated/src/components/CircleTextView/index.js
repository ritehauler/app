// @flow
import React, { Component } from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import { Text } from "../";
import styles from "./styles";

class CircleTextView extends Component {

    static propTypes = {
        title: PropTypes.oneOfType([PropTypes.object, PropTypes.string]),
        customStyle: PropTypes.oneOfType([PropTypes.array, PropTypes.object, PropTypes.number]),
        color: PropTypes.string,
        size: PropTypes.string,
        type: PropTypes.string
    };

    static defaultProps = {
        title: "",
        customStyle: {},
        color: "primary",
        size: "normal",
        type: "medium"
    };

    render() {
        const { title, customStyle, type, color, size, ...rest } = this.props;

        return (
            <View style={[styles.container, customStyle]}>
                <Text
                    style={styles.textStyle}
                    title={title}
                    type={type}
                    color={color}
                    size={size}
                    {...rest} >
                    {title}
                </Text>
            </View>
        );
    }
}

export default CircleTextView;